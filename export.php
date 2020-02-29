<?php

/*

# Taskmaster

This file is part of the Taskmaster project. Taskmaster is a simple task, project, and information tracking application.

# Copyright

Copyright 2013-2015 David Rodgers - <https://github.com/caressofsteel/taskmaster>
Copyright 2012-2013 Alexander Reichardt - <https://github.com/alex-LE/yourTinyTodo>
Copyright 2009-2010 Max Pozdeev - <https://github.com/maxpozdeev/mytinytodo>

This project is distributed under the GNU General Public License. Please see the included COPYRIGHT and LICENSE-GPL3 for more information. Copyrights for portions of this file are retained by their owners.

*/

//$dontStartSession = 1;
require_once('./init.php');

$onlyPublishedList = false;
if (!have_write_access()) $onlyPublishedList = true;

$listId = (int) _get('list');
$listData = $db->sqa("SELECT * FROM {$db->prefix}lists WHERE id=$listId " . ($onlyPublishedList ? "AND published=1" : ""));
if (!$listData) {
	die("No Such List or Access Denied");
}

$sqlSort = "ORDER BY compl ASC, ";
if ($listData['sorting'] == 1) $sqlSort .= "prio DESC, ddn ASC, duedate ASC, ow ASC";
elseif ($listData['sorting'] == 2) $sqlSort .= "ddn ASC, duedate ASC, prio DESC, ow ASC";
else $sqlSort .= "ow ASC";

$data = array();
$q = $db->dq("SELECT *, duedate IS NULL AS ddn FROM {$db->prefix}tasks WHERE list_id=$listId $sqlSort");
while ($r = $q->fetch_assoc($q)) {
	$data[] = $r;
}

$format = _get('format');

if ($format == 'ical') printICal($listData, $data);
else printCSV($listData, $data);


function have_write_access()
{
	if (Config::get('password') == '') return true;
	if (is_logged()) return true;
	return false;
}


function printCSV($listData, $data)
{
	$s = "\xEF\xBB\xBF" . "Completed;Priority;Task;Notes;Tags;Due;DateCreated;DateCompleted\n";
	foreach ($data as $r) {
		$s .= ($r['compl'] ? '1' : '0') . ';' .
			$r['prio'] . ';' . escape_csv($r['title']) . ';' .
			escape_csv($r['note']) . ';' .
			escape_csv($r['tags']) . ';' .
			$r['duedate'] . ';' .
			date('Y-m-d H:i:s O', $r['d_created']) . ';' .
			($r['d_completed'] ? date('Y-m-d H:i:s O', $r['d_completed']) : '') . "\n";
	}
	header('Content-type: text/csv; charset=utf-8');
	header('Content-disposition: attachment; filename=list_' . $listData['id'] . '.csv');
	print $s;
}

function escape_csv($v)
{
	//escape formulas
	$nf = '';
	$trimmed = ltrim($v);
	if (strlen($trimmed) > 0 && in_array(substr($trimmed, 0, 1), array('=', '+', '-', '@'))) {
		$nf = "'";
	}
	return '"' . $nf . str_replace('"', '""', $v) . '"';
}

function printICal($listData, $data)
{
	$taskmasterToIcalPrio = array("1" => 5, "2" => 1);
	$s = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nMETHOD:PUBLISH\r\nCALSCALE:GREGORIAN\r\nPRODID:-//Taskmaster//iCalendar Export v1.4//EN\r\n" .
		"X-WR-CALNAME:" . $listData['name'] . "\r\nX-taskmaster-TIMEZONE:" . Config::get('timezone') . "\r\n";
	# to-do
	foreach ($data as $r) {
		$a = array();
		$a[] = "BEGIN:VTODO";
		$a[] = "UID:" . $r['uuid'];
		$a[] = "CREATED:" . gmdate('Ymd\THis\Z', $r['d_created']);
		$a[] = "DTSTAMP:" . gmdate('Ymd\THis\Z', $r['d_edited']);
		$a[] = "LAST-MODIFIED:" . gmdate('Ymd\THis\Z', $r['d_edited']);
		$a[] = utf8chunks("SUMMARY:" . $r['title']);
		if ($r['duedate']) {
			$dda = explode('-', $r['duedate']);
			$a[] = "DUE;VALUE=DATE:" . sprintf("%u%02u%02u", $dda[0], $dda[1], $dda[2]);
		}
		# Apple's iCal priorities: low-9, medium-5, high-1
		if ($r['prio'] > 0 && isset($taskmasterToIcalPrio[$r['prio']])) $a[] = "PRIORITY:" . $taskmasterToIcalPrio[$r['prio']];
		$a[] = "X-taskmaster-PRIORITY:" . $r['prio'];

		$descr = array();
		if ($r['tags'] != '') $descr[] = Lang::instance()->get('tags') . ": " . str_replace(',', ', ', $r['tags']);
		if ($r['note'] != '') $descr[] = Lang::instance()->get('note') . ": " . $r['note'];
		if ($descr) $a[] = utf8chunks("DESCRIPTION:" . str_replace("\n", '\\n', implode("\n", $descr)));

		if ($r['compl']) {
			$a[] = "STATUS:COMPLETED"; #used in Sunbird
			$a[] = "COMPLETED:" . gmdate('Ymd\THis\Z', $r['d_completed']);
			#$a[] = "PERCENT-COMPLETE:100"; #used in Sunbird
		}
		if ($r['tags'] != '') $a[] = utf8chunks("X-taskmaster-TAGS:" . $r['tags']);
		$a[] = "END:VTODO\r\n";
		$s .= implode("\r\n", $a);
	}
	# events
	foreach ($data as $r) {
		if (!$r['duedate'] || $r['compl']) continue;	# skip tasks completed and without duedate 
		$a = array();
		$a[] = "BEGIN:VEVENT";
		$a[] = "UID:_" . $r['uuid'];	# do not duplicate VTODO UID
		$a[] = "CREATED:" . gmdate('Ymd\THis\Z', $r['d_created']);
		$a[] = "DTSTAMP:" . gmdate('Ymd\THis\Z', $r['d_edited']);
		$a[] = "LAST-MODIFIED:" . gmdate('Ymd\THis\Z', $r['d_edited']);
		$a[] = utf8chunks("SUMMARY:" . $r['title']);
		if ($r['prio'] > 0 && isset($taskmasterToIcalPrio[$r['prio']])) $a[] = "PRIORITY:" . $taskmasterToIcalPrio[$r['prio']];
		$dda = explode('-', $r['duedate']);
		$a[] = "DTSTART;VALUE=DATE:" . sprintf("%u%02u%02u", $dda[0], $dda[1], $dda[2]);
		$a[] = "DTEND;VALUE=DATE:" . date('Ymd', mktime(1, 1, 1, $dda[1], $dda[2], $dda[0]) + 86400);
		$descr = array();
		if ($r['tags'] != '') $descr[] = Lang::instance()->get('tags') . ": " . str_replace(',', ', ', $r['tags']);
		if ($r['note'] != '') $descr[] = Lang::instance()->get('note') . ": " . $r['note'];
		if ($descr) $a[] = utf8chunks("DESCRIPTION:" . str_replace("\n", '\\n', implode("\n", $descr)));
		$a[] = "END:VEVENT\r\n";
		$s .= implode("\r\n", $a);
	}
	$s .= "END:VCALENDAR\r\n";
	header('Content-type: text/calendar; charset=utf-8');
	header('Content-disposition: attachment; filename=list_' . $listData['id'] . '.ics');
	print $s;
}

function utf8chunks($text, $chunklen = 75, $delimiter = "\r\n\t")
{
	if ($text == '') return '';
	preg_match_all('/./u', $text, $m);
	$chars = $m[0];
	$a = array();
	$s = '';
	$max = count($chars);
	for ($i = 0; $i < $max; $i++) {
		$ch = $chars[$i];
		if (strlen($s) + strlen($ch) > $chunklen) { # line should be not more than $chunklen bytes
			$a[] = $s;
			$s = $ch;
		} else $s .= $ch;
	}
	if ($s != '') $a[] = $s;
	return implode($delimiter, $a);
}
