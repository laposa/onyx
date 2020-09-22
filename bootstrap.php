<?php
/**
 *
 * Copyright (c) 2005-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


/**
 * Set include paths
 */

set_include_path(ONYX_PROJECT_DIR . PATH_SEPARATOR . ONYX_DIR . PATH_SEPARATOR . ONYX_DIR . 'lib/' . PATH_SEPARATOR . get_include_path());
require_once('lib/onyx.functions.php');

/**
 * Setup Tracy
 */
if (ONYX_TRACY) {
    require_once('lib/Tracy/tracy.php');
    require_once('lib/Tracy/Onyx/Onyx_Extensions.php');
    if (constant('ONYX_IS_DEBUG_HOST')) {
        $components = array();
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

/**
 * Debug benchmarking
 */
 
if (ONYX_BENCHMARK && ONYX_IS_DEBUG_HOST) {
    $time_start = microtime(true);
    define("TIME_START", $time_start);
}

/**
 * Include Bootstrap
 */

require_once('lib/onyx.bootstrap.php');

/**
 * log to firebug
 */
 
if (ONYX_IS_DEBUG_HOST && ONYX_DEBUG_OUTPUT_FIREBUG) {

    require_once('Zend/Log/Writer/Firebug.php');
    require_once('Zend/Log.php');
    
    // Place this in your bootstrap file before dispatching your front controller
    $writer = new Zend_Log_Writer_Firebug();
    $GLOBALS['fb_logger'] = new Zend_Log($writer);
    
    require_once('Zend/Controller/Request/Http.php');
    $request = new Zend_Controller_Request_Http();
    require_once('Zend/Controller/Response/Http.php');
    $response = new Zend_Controller_Response_Http();

    $channel = Zend_Wildfire_Channel_HttpHeaders::getInstance();
    $channel->setRequest($request);
    $channel->setResponse($response);
}


/**
 * Init Bootstrap
 */

$Bootstrap = new Onyx_Bootstrap();

/**
 * Init pre-action (standard pre-actions defined as global variable in conf/global.php)
 */

$Bootstrap->initPreAction($onyx_pre_actions);

/**
 * Init action
 */
    
$Bootstrap->initAction($_GET['request']);

/**
 * test log to firebug
 */
 
if (ONYX_IS_DEBUG_HOST && isset($channel) && isset($response)) {

    // Flush log data to browser
    $channel->flush();
    $response->sendHeaders();  
}


/**
 * Output content
 */

echo $Bootstrap->finalOutput();


/**
 * Debug benchmarking
 */
   
if (ONYX_BENCHMARK && ONYX_IS_DEBUG_HOST) {
    $time_end = microtime(true);
    $time = $time_end - $time_start;
    $time = round($time, 4);
    echo "<div class='onyx_messages'><p class='onyx_ok_msg'>Script total running time = $time sec.</p>";
    echo "<p class='onyx_ok_msg'>Total Memory Usage = " . round((memory_get_peak_usage()/1024)/1024, 2) . "MB</p>";
    echo '</div>';
}

if (ONYX_DB_PROFILER && ONYX_IS_DEBUG_HOST) {
    $db = Zend_Registry::get('onyx_db');
    $profiler = $db->getProfiler();
    $db_profile = array();
    $db_profile['total_num_queries'] = $profiler->getTotalNumQueries();
    $db_profile['total_elapsed_secs'] = $profiler->getTotalElapsedSecs();
    $db_profile['query_list'] = array();
    
    foreach ($profiler->getQueryProfiles() as $k=>$item) {
    
        $db_profile['query_list'][$k]['query'] = $item->getQuery();
        $db_profile['query_list'][$k]['query_params'] = $item->getQueryParams();
        $db_profile['query_list'][$k]['elapsed_secs'] = $item->getElapsedSecs();
    }
    
    echo "<pre>" . htmlspecialchars(print_r($db_profile, true)) . "</pre>";
}


