<?php
/**
 *
 * Copyright (c) 2005-2020 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

// Set include paths
set_include_path(ONYX_PROJECT_DIR . PATH_SEPARATOR . ONYX_DIR . PATH_SEPARATOR . ONYX_DIR . 'lib/' . PATH_SEPARATOR . get_include_path());
require_once('lib/onyx.functions.php');

// Setup Tracy
if (ONYX_TRACY) {
    require_once('lib/Tracy/tracy.php');
    require_once('lib/Tracy/Onyx/Onyx_Extensions.php');
    if (constant('ONYX_IS_DEBUG_HOST')) {
        $components = [];
        Tracy\Debugger::enable(Tracy\Debugger::DEVELOPMENT);
        if (ONYX_TRACY_DB_PROFILER) Tracy\Debugger::getBar()->addPanel(new DBProfilerPanel);
        if (ONYX_TRACY_BENCHMARK) Tracy\Debugger::getBar()->addPanel(new ComponentsPanel);
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
$bootstrap->initAction($_GET['request']);

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
    $profiler = $db->getProfiler();
    $db_profile = [];
    $db_profile['total_num_queries'] = $profiler->getTotalNumQueries();
    $db_profile['total_elapsed_secs'] = $profiler->getTotalElapsedSecs();
    $db_profile['query_list'] = [];

    foreach ($profiler->getQueryProfiles() as $k => $item) {
        $db_profile['query_list'][$k]['query'] = $item->getQuery();
        $db_profile['query_list'][$k]['query_params'] = $item->getQueryParams();
        $db_profile['query_list'][$k]['elapsed_secs'] = $item->getElapsedSecs();
    }

    echo "<pre>" . htmlspecialchars(print_r($db_profile, true)) . "</pre>";
}


