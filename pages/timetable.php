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

require_once('../ajax.php');

$db = DBConnection::instance();

$hours_per_day = (intval(Config::get('timetable_day')) > 0) ? intval(Config::get('timetable_day')) : 8;

$list_id = (isset($_GET['listid']) && intval($_GET['listid']) > 0) ? intval($_GET['listid']) : 0;
if (empty($list_id)) {
	die('Error!');
}

$q = $db->dq("SELECT * FROM {$db->prefix}lists WHERE id = " . $list_id . " ORDER BY ow ASC, id ASC");
$list_data = $q->fetch_assoc($q);

$list = loadTasks($list_id, 0, '', '', 1);
$total_time = 0;
$total_progress = 0;
foreach ($list['list'] as $item) {
	$total_time += floatval($item['duration_h'] + $item['duration_m'] / 60);
	$total_progress += floatval($item['progress_current']);
}

if ($total_time > 0) {
	$total_progress_value = round((100 * $total_progress) / $total_time);
	$total_time_value = (100 - $total_progress_value);
} else {
	$total_progress_value = 0;
	$total_time_value = 100;
}

$total_days = 0;
$total_hours = 0;
$total_minutes = 0;
$has_days = '';

if ($total_time > $hours_per_day) {
	$total_days = floor($total_time / $hours_per_day);
	$total_hours = floor($total_time - ($total_days * $hours_per_day));
	$total_minutes = round(($total_time - ($total_days * $hours_per_day) - $total_hours) * 60, 0);
	$has_days = 'has_days';
} else {
	$total_hours = floor($total_time);
	$total_minutes = round(($total_time - $total_hours) * 60, 0);
}
?>

<div id="chart_container"></div>
<div id="chart_text"></div>
<div id="tasktable">
	<h3><?= $list_data['name'] ?> - Timetable</h3>
	<ul>
		<?php
		foreach ($list['list'] as $item) {
			$bar_width = ($item['progress'] >= 100) ? '520' : round(($item['progress'] * 520) / 100);
			if ($item['progress'] >= 100) {
				$bar_color = '#ff3333';
			} elseif ($item['progress'] >= 80) {
				$bar_color = '#ffcd20';
			} else {
				$bar_color = '#99d25c';
			}

			$cl = '';
			$v = '';
			if ($item['prio'] < 0) {
				$cl = 'prio-neg prio-neg-' . abs($item['prio']);
				$v = '&#8722;' . abs($item['prio']);				// &#8722; = &minus; = −
			} elseif ($item['prio'] == 0) {
				$cl = 'prio-zero';
				$v = '+' . $item['prio'];
			} elseif ($item['prio'] == 1 || $item['prio'] == 2) {
				$cl = 'prio-pos prio-pos-' . $item['prio'];
				$v = '+' . $item['prio'];
			} else {
				$cl = 'prio-mile';
				$v = 'M';									// &#177; = &plusmn; = ±
			}
		?>
			<li>
				<span class="task-prio <?= $cl ?>"><?= $v ?></span>
				<span class="task-title"><?= $item['title'] ?></span>
				<?php if (!empty($item['duration'])) { ?>
					<span class="duration">
						<?= floor($item['duration']) ?>h <?= (($item['duration'] - floor($item['duration'])) * 60) ?>m
						<img src="<?= 'themes/' . Config::get('template') . '/' ?>images/timetable_edge.png" alt="" />
					</span>
				<?php } ?>
				<span class="taskmaster-progress-bar">
					<span class="taskmaster-progress-percentbar" style="width:<?= $bar_width ?>px;background-color: <?= $bar_color ?>"></span>
				</span>
			</li>
		<?php } ?>
	</ul>
</div>

<script type="text/javascript">
	$(function() {
		var chart;
		$(document).ready(function() {
			var progressData = [];

			progressData.push({
				name: 'rest',
				y: <?= $total_time_value ?>,
				color: '#ECECEC'
			});
			progressData.push({
				name: 'current',
				y: <?= $total_progress_value ?>,
				color: '#80C038'
			});

			// Create the chart
			chart = new Highcharts.Chart({
					chart: {
						renderTo: 'chart_container',
						type: 'pie',
						marginTop: 0,
						marginLeft: 0,
						marginRight: 0,
						marginBottom: 0
					},
					credits: {
						enabled: false
					},
					title: {
						text: ''
					},
					yAxis: {
						title: {
							text: ''
						}
					},
					plotOptions: {
						pie: {
							shadow: false,
							dataLabels: {
								enabled: false
							},
							enableMouseTracking: false,
							borderColor: '#cbcbcb',
							borderWidth: 1
						}
					},
					series: [{
						name: 'Progress',
						data: progressData,
						innerSize: '45%'
					}]
				},
				function(chart) { // on complete
					var textX = chart.plotLeft + (chart.plotWidth * 0.5);
					var textY = chart.plotTop + (chart.plotHeight * 0.5);

					var span = '<span id="pieChartInfoText" class="<?= $has_days ?>">';
					span += '<span id="total_caption"><?= _e('tt_total') ?></span><br>';
					span += '<span id="total_time_days"><?= $total_days ?> <?= _e('tt_days') ?></span>';
					span += '<span id="total_time_hours"><?= $total_hours ?>h <?= $total_minutes ?>m</span>';
					span += '</span>';

					$("#chart_text").append(span);
				}
			);
		});

	});
</script>