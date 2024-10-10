<?php


class OnyxComponentsPanel implements \Tracy\IBarPanel {
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
            $content .= isset($component['node']) ? "<td>" . $component['node'] . "</td>" : '';
            $content .= isset($component['controller']) ? "<td>" . $component['controller'] . "</td>" : '';
            $content .= "<td>" . format_time($time) . "</td>";
            $content .= "</tr>";
        }
        $content .= "</table></div>";

        return $content;
    }
}
