<?php
/**
 *
 * Copyright (c) 2005-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


/**
 * Set include paths
 */

set_include_path(ONXSHOP_PROJECT_DIR . PATH_SEPARATOR . ONXSHOP_DIR . PATH_SEPARATOR . ONXSHOP_DIR . 'lib/' . PATH_SEPARATOR . get_include_path());
require_once('lib/onxshop.functions.php');

/**
 * Debug benchmarking
 */
 
if (ONXSHOP_BENCHMARK && ONXSHOP_IS_DEBUG_HOST) {
	$time_start = microtime(true);
	define("TIME_START", $time_start);
}

/**
 * Include Bootstrap
 */

require_once('lib/onxshop.bootstrap.php');

/**
 * log to firebug
 */
 
if (ONXSHOP_IS_DEBUG_HOST && ONXSHOP_DEBUG_OUTPUT_FIREBUG) {

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

$Bootstrap = new Onxshop_Bootstrap();

/**
 * Init pre-action (standard pre-actions defined as global variable in conf/global.php)
 */

$Bootstrap->initPreAction($onxshop_pre_actions);

/**
 * Init action
 */
	
$Bootstrap->initAction($_GET['request']);

/**
 * test log to firebug
 */
 
if (ONXSHOP_IS_DEBUG_HOST && ONXSHOP_DEBUG_OUTPUT_FIREBUG) {

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
   
if (ONXSHOP_BENCHMARK && ONXSHOP_IS_DEBUG_HOST) {
    $time_end = microtime(true);
    $time = $time_end - $time_start;
    $time = round($time, 4);
    echo "<div class='onxshop_messages'><p class='onxshop_ok_msg'>Script total running time = $time sec.</p>";
    echo "<p class='onxshop_ok_msg'>Total Memory Usage = " . round((memory_get_usage()/1024)/1024, 2) . "MB</p>";
    echo '</div>';
}

if (ONXSHOP_DB_PROFILER) {
	$db = Zend_Registry::get('onxshop_db');
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


