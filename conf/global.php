<?php
/**
 * Global Onxshop configuration
 *
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 	
 */

/**
 * Force using only one domain
 */

//define('ONXSHOP_MAIN_DOMAIN', 'default.co.uk');

if (defined('ONXSHOP_MAIN_DOMAIN')) {
	if ($_SERVER['HTTPS']) $protocol = 'https';
	else $protocol = 'http';
	
	if ($_SERVER['HTTP_HOST'] != ONXSHOP_MAIN_DOMAIN) {
	    Header( "HTTP/1.1 301 Moved Permanently" );
	    Header( "Location: $protocol://" . ONXSHOP_MAIN_DOMAIN . "{$_SERVER['REQUEST_URI']}" );
	    //exit the application immediately 
	    exit;
	}
}

/**
 * Set output header
 */

header( 'Content-Type: text/html; charset=UTF-8' );

/**
 * Can the remote host see debugging messages?
 */
 
if(in_array($_SERVER["REMOTE_ADDR"], array_keys($debug_hosts)))  {
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set('display_errors', 1);
	define('ONXSHOP_DEBUG_LEVEL', $debug_hosts[$_SERVER["REMOTE_ADDR"]]);
	define('ONXSHOP_IS_DEBUG_HOST', true);
	define('ONXSHOP_DEBUG_DIRECT', false);
	define('ONXSHOP_DEBUG_FILE', true);
	define('ONXSHOP_BENCHMARK', true);
	define('ONXSHOP_DB_PROFILER', true);
	
} else {
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set('display_errors', 0);
	define('ONXSHOP_DEBUG_LEVEL', 0);
	define('ONXSHOP_IS_DEBUG_HOST', false);
	define('ONXSHOP_DEBUG_DIRECT', false);
	define('ONXSHOP_DEBUG_FILE', true);
	define('ONXSHOP_BENCHMARK', false);
	define('ONXSHOP_DB_PROFILER', false);
	
}


/**
 * Compress output option (not really important when using Apache Deflate module)
 */
 
if (!defined('ONXSHOP_COMPRESS_OUTPUT')) define('ONXSHOP_COMPRESS_OUTPUT', 0);


/**
 * Session type (database, file)
 */

if (!defined('ONXSHOP_SESSION_TYPE')) define('ONXSHOP_SESSION_TYPE', 'database');


/**
 * Layout settings
 */
 
if (!defined('ONXSHOP_DEFAULT_TYPE')) define('ONXSHOP_DEFAULT_TYPE', 'sys/xhtml_10-trans');
if (!defined('ONXSHOP_MAIN_TEMPLATE')) define('ONXSHOP_MAIN_TEMPLATE', 'node/site/default');
if (!defined('ONXSHOP_PAGE_TEMPLATE')) define('ONXSHOP_PAGE_TEMPLATE', 'node/page/default');
if (!defined('ONXSHOP_DEFAULT_LAYOUT')) define('ONXSHOP_DEFAULT_LAYOUT', ONXSHOP_DEFAULT_TYPE . '.' . ONXSHOP_MAIN_TEMPLATE);


//hack
if (isset($_GET['preview']) && $_GET['preview'] == 1) {
	$_SESSION['preview'] = 1;
} else if (isset($_GET['exit_preview']) && $_GET['exit_preview'] == 1) {
	$_SESSION['preview'] = 0;
}

/**
 * detect if any backoffice controller is called
 */
 
if (preg_match("/\.bo\//", $_GET['request'])) define('ONXSHOP_IN_BACKOFFICE', true);
else define('ONXSHOP_IN_BACKOFFICE', false);

//use query cache?
if (ONXSHOP_IN_BACKOFFICE) {
	//should be here, but it's not working here :)
	// looks like ONXSHOP_IN_BACKOFFICE detection above isn't working
	//temporarily moved to bootstrap.php
	//define('ONXSHOP_DB_QUERY_CACHE', false);
} else {
	//define('ONXSHOP_DB_QUERY_CACHE', true);
}

if (!defined('ONXSHOP_DB_QUERY_CACHE_TTL')) define('ONXSHOP_DB_QUERY_CACHE_TTL', 3600);
if (!defined('ONXSHOP_DB_QUERY_CACHE_DIRECTORY')) define('ONXSHOP_DB_QUERY_CACHE_DIRECTORY', ONXSHOP_PROJECT_DIR . 'var/cache/');

/* Zend Cache for whole page */
if (!defined('ONXSHOP_PAGE_CACHE_TTL')) define('ONXSHOP_PAGE_CACHE_TTL', 86400);


/**
 * Onxshop package name
 * 
 * (basic, standard, premium)
 * 
 * basic - only CMS
 * standard - CMS + eCommerce
 * premium - CMS + eCommerce + available backup download
 */

if (!defined('ONXSHOP_PACKAGE_NAME')) define('ONXSHOP_PACKAGE_NAME', 'standard');

/**
 * Onxshop system support email
 */
 
if (!defined('ONXSHOP_SUPPORT_EMAIL')) define('ONXSHOP_SUPPORT_EMAIL', 'support@onxshop.com');
if (!defined('ONXSHOP_SUPPORT_NAME')) define('ONXSHOP_SUPPORT_NAME', 'Onxshop support team');
