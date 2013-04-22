<?php
/**
 * Global Onxshop configuration
 *
 * Copyright (c) 2005-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 	
 */

/**
 * Force using only one domain
 */

//define('ONXSHOP_MAIN_DOMAIN', 'default.co.uk');

if (defined('ONXSHOP_MAIN_DOMAIN')) {
	if (array_key_exists('HTTPS', $_SERVER)) $protocol = 'https';
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
	define('ONXSHOP_DEBUG_FIREBUG', false);
	define('ONXSHOP_BENCHMARK', false);
	define('ONXSHOP_DB_PROFILER', false);
	
} else {
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set('display_errors', 0);
	define('ONXSHOP_DEBUG_LEVEL', 0);
	define('ONXSHOP_IS_DEBUG_HOST', false);
	define('ONXSHOP_DEBUG_DIRECT', false);
	define('ONXSHOP_DEBUG_FILE', true);
	define('ONXSHOP_DEBUG_FIREBUG', false);
	define('ONXSHOP_BENCHMARK', false);
	define('ONXSHOP_DB_PROFILER', false);
	
}

/**
 * Authentication type for backend users
 */

if (!defined('ONXSHOP_AUTH_TYPE')) define('ONXSHOP_AUTH_TYPE', 'postgresql');
if (!defined('ONXSHOP_AUTH_SERVER')) define('ONXSHOP_AUTH_SERVER', ONXSHOP_DB_HOST);

/**
 * HardCoded user/password for backend users
 *
 */

//if (!defined('ONXSHOP_EDITOR_USERNAME')) define('ONXSHOP_EDITOR_USERNAME', 'site_editor_username');
//if (!defined('ONXSHOP_EDITOR_PASSWORD')) define('ONXSHOP_EDITOR_PASSWORD', 'site_password_password');

/**
 * Is authentication always required?
 */

if (!defined('ONXSHOP_REQUIRE_AUTH')) define('ONXSHOP_REQUIRE_AUTH', false);

/**
 * Use SSL?
 */

if (!defined('ONXSHOP_EDITOR_USE_SSL')) define('ONXSHOP_EDITOR_USE_SSL', false);
if (!defined('ONXSHOP_CUSTOMER_USE_SSL')) define('ONXSHOP_CUSTOMER_USE_SSL', false);

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
 
if (!defined('ONXSHOP_DEFAULT_TYPE')) define('ONXSHOP_DEFAULT_TYPE', 'sys/html5');
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

if (!defined('ONXSHOP_SUPPORT_WEBSITE_URL')) define('ONXSHOP_SUPPORT_WEBSITE_URL', 'https://onxshop.com/support');
if (!defined('ONXSHOP_SUPPORT_EMAIL')) define('ONXSHOP_SUPPORT_EMAIL', 'support@onxshop.com');
if (!defined('ONXSHOP_SUPPORT_NAME')) define('ONXSHOP_SUPPORT_NAME', 'Onxshop support team');


/**
 * allow backup download
 */

if (!defined('ONXSHOP_ALLOW_BACKUP_DOWNLOAD')) define('ONXSHOP_ALLOW_BACKUP_DOWNLOAD', true);

/**
 * forwarding URLs
 */
 
if (!defined('BASKET_CONTINUE_SHOPPING_URL')) define('BASKET_CONTINUE_SHOPPING_URL', '/');
if (!defined('AFTER_CLIENT_LOGOUT_URL')) define('AFTER_CLIENT_LOGOUT_URL', '/');

/**
 * allow scheduler
 */

if (!defined('ONXSHOP_ALLOW_SCHEDULER')) define('ONXSHOP_ALLOW_SCHEDULER', false);

/**
 * allow search index update on cache save event
 */

if (!defined('ONXSHOP_ALLOW_SEARCH_INDEX_AUTOUPDATE')) define('ONXSHOP_ALLOW_SEARCH_INDEX_AUTOUPDATE', false);

/**
 * Set pre-action list as array
 */

$onxshop_pre_actions = array("autologin", "locales");
if (ONXSHOP_ALLOW_SCHEDULER) $onxshop_pre_actions[] = "scheduler";
