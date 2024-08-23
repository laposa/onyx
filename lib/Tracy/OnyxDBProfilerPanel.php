<?php

require_once('lib/onyx.container.php');

class OnyxDBProfilerPanel implements \Tracy\IBarPanel
{

	function getTab()
	{
		return '<img src="/share/images/famfamfam_icons/database.png"/> <span>Database</span>';
	}

	function getPanel()
	{
	    /** @var \Doctrine\DBAL\Connection $db */
		$db = Onyx_Container::getInstance()->get('onyx_db');

		$content = '<h1>Database Profiler</h1><div class="tracy-inner">';
		$content .= "<table>";

		$controller = "bootstrap";
		$previous_controller = false;
		$i = 0;

		/** @var OnyxSQLLogger $sqlLogger */
		if ($db) {
			$sqlLogger = $db->getConfiguration()->getSQLLogger();
			foreach ($sqlLogger->queries as $item) {

				while (isset($GLOBALS['components'][$i]) &&
					$GLOBALS['components'][$i]['time'] < $item['startMS']) {
					$controller = $GLOBALS['components'][$i]['controller'];
					$i++;
				}

				if ($previous_controller != $controller) {
					$previous_controller = $controller;
					$content .= '<tr>';
					$content .= '<th colspan="3"><strong>' . $controller . '</strong></th>';
					$content .= '</tr>';
				}

				$sql = $this->format(trim($item['sql']));
				$sql = str_replace('background-color: white', 'background-color: none', $sql);
				$elapsed = $item['executionMS'];

				$content .= "<tr>";
				if ($elapsed > 1) $content .= '<td><img src="/share/images/famfamfam_icons/exclamation.png" /></td>';
				else if ($elapsed > 0.4) $content .= '<td><img src="/share/images/famfamfam_icons/error.png" /></td>';
				else $content .= "<td>&nbsp;</td>";
				$content .= "<td>" . $sql . "</td>";
				$content .= "<td>" . format_time($elapsed) . "</td>";
				$content .= "</tr>";
			}

			$content .= "<tr><th></th><th>Total " . count($sqlLogger->queries) . " queries</th>";
			$content .= "<th>" . format_time($sqlLogger->totalExecutionMS) . "</th>";
			
		} else {
			$content .= "<tr><td>No database connection used.</td></tr>";
		}

		$content .= "</table></div>";
		return $content;
	}

	function format($sql) {
		if (strlen($sql) > 100 || strpos($sql, "\n")) return SqlFormatter::format($sql);
		else return SqlFormatter::highlight($sql);
	}
}
