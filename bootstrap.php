<?php
/**
 *
 * Copyright (c) 2005-2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

// Setup Tracy
if (ONYX_TRACY) {
    require_once('lib/Tracy/OnyxComponentsPanel.php');
    require_once('lib/Tracy/OnyxDBProfilerPanel.php');
    if (constant('ONYX_IS_DEBUG_HOST')) {
        $components = [];
        Tracy\Debugger::enable(Tracy\Debugger::DEVELOPMENT);
        if (ONYX_TRACY_DB_PROFILER) Tracy\Debugger::getBar()->addPanel(new OnyxDBProfilerPanel());
        if (ONYX_TRACY_BENCHMARK) Tracy\Debugger::getBar()->addPanel(new OnyxComponentsPanel());
    } else {
        Tracy\Debugger::enable(Tracy\Debugger::PRODUCTION);
        Tracy\Debugger::$logSeverity = (E_ALL ^ E_NOTICE);
        Tracy\Debugger::$logDirectory = ONYX_PROJECT_DIR . "/var/log";
        if (constant('ONYX_ERROR_EMAIL')) Tracy\Debugger::$email = ONYX_ERROR_EMAIL;
    }
    error_reporting(E_ALL ^ E_NOTICE);
}

// Debug benchmarking
if (ONYX_BENCHMARK && ONYX_IS_DEBUG_HOST) {
    $time_start = microtime(true);
    define("TIME_START", $time_start);
}

// Include & init dependency injection container
require_once('lib/onyx.container.php');
$container = Onyx_Container::getInstance();

// Include & init Bootstrap
require_once('lib/onyx.bootstrap.php');
$bootstrap = new Onyx_Bootstrap();

// Init pre-action (standard pre-actions defined as global variable in conf/global.php)
if (!isset($onyx_pre_actions)) $onyx_pre_actions = [];
$bootstrap->initPreAction($onyx_pre_actions);

// Init action
$bootstrap->initAction($_GET['request'] ?? '');

// Test log to firebug
if (ONYX_IS_DEBUG_HOST && isset($channel) && isset($response)) {
    // Flush log data to browser
    $channel->flush();
    $response->sendHeaders();
}

// Output content
echo $bootstrap->finalOutput();

// Debug benchmarking
if (ONYX_BENCHMARK && ONYX_IS_DEBUG_HOST) {
    $time_end = microtime(true);
    $time = $time_end - $time_start;
    $time = round($time, 4);
    echo "<div class='onyx_messages'><p class='onyx_ok_msg'>Script total running time = $time sec.</p>";
    echo "<p class='onyx_ok_msg'>Total Memory Usage = " . round((memory_get_peak_usage() / 1024) / 1024, 2) . "MB</p>";
    echo '</div>';
}

if (ONYX_DB_PROFILER && ONYX_IS_DEBUG_HOST) {
    $db = $container->get('onyx_db');
    if ($db) {
        $logger = $db->getConfiguration()->getSQLLogger();
        $db_profile = [];
        $db_profile['total_num_queries'] = count($logger->queries);
        $db_profile['total_elapsed_secs'] = $logger->totalExecutionMS;
        $db_profile['query_list'] = [];

        foreach ($logger->queries as $k => $item) {
            $db_profile['query_list'][$k]['query'] = $item['sql'];
            $db_profile['query_list'][$k]['query_params'] = $item['params'];
            $db_profile['query_list'][$k]['elapsed_secs'] = $item['executionMS'];
        }

        echo "<pre>" . htmlspecialchars(print_r($db_profile, true)) . "</pre>";
    }
}


