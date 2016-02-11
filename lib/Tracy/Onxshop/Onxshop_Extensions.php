<?php

require_once("lib/SqlFormatter/SqlFormatter.php");

class DBProfilerPanel implements Tracy\IBarPanel
{

	function getTab()
	{
		return '<img src="/share/images/famfamfam_icons/database.png"/> <span>Database</span>';
	}

	function getPanel()
	{
		$db = Zend_Registry::get('onxshop_db');
		$profiler = $db->getProfiler();

		$content = '<h1>Database Profiler</h1><div class="tracy-inner">';
		$content .= "<table>";

		$controller = "bootstrap";
		$previous_controller = false;
		$i = 0;

		foreach ($profiler->getQueryProfiles() as $item) {

			while (isset($GLOBALS['components'][$i]) && 
				$GLOBALS['components'][$i]['time'] < $item->getStartedMicrotime()) {
				$controller = $GLOBALS['components'][$i]['controller'];
				$i++;
			}

			if ($previous_controller != $controller) {
				$previous_controller = $controller;
				$content .= '<tr>';
				$content .= '<th colspan="3"><strong>' . $controller . '</strong></th>';
				$content .= '</tr>';
			}

			$sql = $this->format(trim($item->getQuery()));
			$sql = str_replace('background-color: white', 'background-color: none', $sql);
			$elapsed = $item->getElapsedSecs();

			$content .= "<tr>";
			if ($elapsed > 1) $content .= '<td><img src="/share/images/famfamfam_icons/exclamation.png" /></td>';
			else if ($elapsed > 0.4) $content .= '<td><img src="/share/images/famfamfam_icons/error.png" /></td>';
			else $content .= "<td>&nbsp;</td>";
			$content .= "<td>" . $sql . "</td>";
			$content .= "<td>" . format_time($elapsed) . "</td>";
			$content .= "</tr>";
		}

		$content .= "<tr><th></th><th>Total " . $profiler->getTotalNumQueries() . " queries</th>";
		$content .= "<th>" . format_time($profiler->getTotalElapsedSecs()) . "</th>";
		$content .= "</table></div>";

		return $content;
	}

	function format($sql) {
		if (strlen($sql) > 100 || strpos($sql, "\n")) return SqlFormatter::format($sql);
		else return SqlFormatter::highlight($sql);
	}
}

class ComponentsPanel implements Tracy\IBarPanel
	{

	function getTab()
	{
		return '<img src="/share/images/famfamfam_icons/bricks.png"/> <span>Components</span>';
	}

	function getPanel()
	{
		$content = '<h1>Components</h1><div class="tracy-inner">';
		$content .= "<table>";
		$content .= "<tr><th>Node Title</th><th>Controller</th><th>Duration</th>";
		foreach ($GLOBALS['components'] as $i => $component) {
			if (isset($GLOBALS['components'][$i + 1])) $time = $GLOBALS['components'][$i + 1]['time'] - $component['time'];
			else $time = microtime(true) - $component['time']; 
			$content .= "<tr>";
			$content .= "<td>" . $component['node'] . "</td>";
			$content .= "<td>" . $component['controller'] . "</td>";
			$content .= "<td>" . format_time($time) . "</td>";
			$content .= "</tr>";
		}
		$content .= "</table></div>";

		return $content;
	}
}
