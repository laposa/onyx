<?php
/**
 * Global Onxshop configuration
 *
 * Copyright (c) 2005-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 	
 */

/**
 * Force using only one domain
 */

//define('ONXSHOP_MAIN_DOMAIN', 'default.co.uk');

/**
 * Can the remote host see debugging messages?
 */
 
if(in_array($_SERVER["REMOTE_ADDR"], array_keys($debug_hosts)))  {
	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
	ini_set('display_errors', 1);
	define('ONXSHOP_DEBUG_LEVEL', $debug_hosts[$_SERVER["REMOTE_ADDR"]]);
	define('ONXSHOP_IS_DEBUG_HOST', true);
	define('ONXSHOP_DEBUG_DIRECT', false);
	define('ONXSHOP_DEBUG_FILE', true);
	define('ONXSHOP_DEBUG_FIREBUG', false);
	define('ONXSHOP_BENCHMARK', false);
	define('ONXSHOP_DB_PROFILER', false);
	
} else {
	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
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

/**
 * cache backend possible values: File, Apc
 */
 
if (!defined('ONXSHOP_DB_QUERY_CACHE_BACKEND')) define('ONXSHOP_DB_QUERY_CACHE_BACKEND', 'File'); // change of this will change also ONXSHOP_PAGE_CACHE_BACKEND, see below
if (!defined('ONXSHOP_DB_QUERY_CACHE_TTL')) define('ONXSHOP_DB_QUERY_CACHE_TTL', 3600);
if (!defined('ONXSHOP_DB_QUERY_CACHE_DIRECTORY')) define('ONXSHOP_DB_QUERY_CACHE_DIRECTORY', ONXSHOP_PROJECT_DIR . 'var/cache/');

/*
 * Zend Cache for whole page
 * set 0 to disable
 */
 
if (!defined('ONXSHOP_PAGE_CACHE_BACKEND')) define('ONXSHOP_PAGE_CACHE_BACKEND', ONXSHOP_DB_QUERY_CACHE_BACKEND); // Same as DB cache
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

if (!defined('ONXSHOP_ALLOW_SCHEDULER')) define('ONXSHOP_ALLOW_SCHEDULER', true);

/**
 * allow search index update on cache save event
 */

if (!defined('ONXSHOP_ALLOW_SEARCH_INDEX_AUTOUPDATE')) define('ONXSHOP_ALLOW_SEARCH_INDEX_AUTOUPDATE', false);

/**
 * social: Facebook
 */
 
if (!defined('ONXSHOP_FACEBOOK_APP_ID')) define('ONXSHOP_FACEBOOK_APP_ID', 0);
if (!defined('ONXSHOP_FACEBOOK_APP_SECRET')) define('ONXSHOP_FACEBOOK_APP_SECRET', '');
if (!defined('ONXSHOP_FACEBOOK_APP_NAMESPACE')) define('ONXSHOP_FACEBOOK_APP_NAMESPACE', '');
if (!defined('ONXSHOP_FACEBOOK_APP_OG_STORIES')) define('ONXSHOP_FACEBOOK_APP_OG_STORIES', '');
if (!defined('ONXSHOP_FACEBOOK_OG_IMAGE')) define('ONXSHOP_FACEBOOK_OG_IMAGE', 'var/files/favicon.ico');
// Canvas Facebook Page is defined by APP_NAMESPACE
define('ONXSHOP_FACEBOOK_CANVAS_PAGE', 'https://apps.facebook.com/' . ONXSHOP_FACEBOOK_APP_NAMESPACE);
// determine environment (desktop vs mobile)
if (($_POST['signed_request'] && $_POST['fb_locale'])) define('ONXSHOP_FACEBOOK_ENV', 'desktop');
else if ($_GET['ref'] == 'web_canvas') define('ONXSHOP_FACEBOOK_ENV', 'mobile');
else define('ONXSHOP_FACEBOOK_ENV', '');
// determine if we are running under Facebook App
if (ONXSHOP_FACEBOOK_ENV == 'desktop' || ONXSHOP_FACEBOOK_ENV == 'mobile') define('ONXSHOP_FACEBOOK_WITHIN_APP', true);
else define('ONXSHOP_FACEBOOK_WITHIN_APP', false);

/**
 * social: Twitter
 */
 
if (!defined('ONXSHOP_TWITTER_APP_ID')) define('ONXSHOP_TWITTER_APP_ID', '');
if (!defined('ONXSHOP_TWITTER_APP_SECRET')) define('ONXSHOP_TWITTER_APP_SECRET', '');
if (!defined('ONXSHOP_TWITTER_USERNAME')) define('ONXSHOP_TWITTER_USERNAME', 'onxshop');
if (!defined('ONXSHOP_TWITTER_HASHTAG')) define('ONXSHOP_TWITTER_HASHTAG', ''); //without hash
if (!defined('ONXSHOP_TWITTER_ACCESS_TOKEN')) define('ONXSHOP_TWITTER_ACCESS_TOKEN', '');
if (!defined('ONXSHOP_TWITTER_ACCESS_TOKEN_SECRET')) define('ONXSHOP_TWITTER_ACCESS_TOKEN_SECRET', '');

/**
 * pagination
 */
 
if (!defined('ONXSHOP_PAGINATION_SHOW_ITEMS')) define('ONXSHOP_PAGINATION_SHOW_ITEMS', 10);

/**
 * Salt used for encryption/hashing
 */

if (!defined('ONXSHOP_ENCRYPTION_SALT'))  define('ONXSHOP_ENCRYPTION_SALT', '');

/**
 * enable A/B testing
 */

if (!defined('ONXSHOP_ENABLE_AB_TESTING')) define('ONXSHOP_ENABLE_AB_TESTING', false);

/**
 * CDN for images
 *
 * ONXSHOP_CDN - use CDN (true/false)
 * ONXSHOP_CDN_HOST - static content service node URL
 * ONXSHOP_CDN_USE_WHEN_SSL - use CDN when request is served on SSL (true/false)
 * ONXSHOP_CDN_ALLOWED_CONTEXT - comma seperated list of html tags (img, link, a, script, style)
 * ONXSHOP_CDN_ALLOWED_TYPES - comma seperated list of file extensions (e.g. 'jpg, gif, png')
 */

if (!defined('ONXSHOP_CDN')) define('ONXSHOP_CDN', false);
if (!defined('ONXSHOP_CDN_HOST')) define('ONXSHOP_CDN_HOST', 'http://static-image.server.my');
if (!defined('ONXSHOP_CDN_USE_WHEN_SSL')) define('ONXSHOP_CDN_USE_WHEN_SSL', false);
if (!defined('ONXSHOP_CDN_ALLOWED_CONTEXT')) define('ONXSHOP_CDN_ALLOWED_CONTEXT', 'img');
if (!defined('ONXSHOP_CDN_ALLOWED_TYPES')) define('ONXSHOP_CDN_ALLOWED_TYPES', 'jpg, gif, png');

/**
 * recaptcha
 */
 
if (!defined('ONXSHOP_RECAPTCHA_PUBLIC_KEY')) define('ONXSHOP_RECAPTCHA_PUBLIC_KEY', '');
if (!defined('ONXSHOP_RECAPTCHA_PRIVATE_KEY')) define('ONXSHOP_RECAPTCHA_PRIVATE_KEY', '');

/**
 * is backoffice user account required?
 */

if (!defined('ONXSHOP_BACKOFFICE_REQUIRE_CUSTOMER_LOGIN')) define('ONXSHOP_BACKOFFICE_REQUIRE_CUSTOMER_LOGIN', false);

/**
 * allow to upload files to root folder in media library?
 */

if (!defined('ONXSHOP_MEDIA_LIBRARY_ROOT_UPLOAD')) define('ONXSHOP_MEDIA_LIBRARY_ROOT_UPLOAD', true);

/**
 * mark object as invalid when using null on required attributes?
 */

if (!defined('ONXSHOP_MODEL_STRICT_VALIDATION')) define('ONXSHOP_MODEL_STRICT_VALIDATION', false);

/**
 * allow to merge newly registred account with previous one
 */

if (!defined('ONXSHOP_CUSTOMER_ALLOW_ACCOUNT_MERGE')) define('ONXSHOP_CUSTOMER_ALLOW_ACCOUNT_MERGE', false);

/**
 * simple translation (output regex filter)
 */

if (!defined('ONXSHOP_SIMPLE_TRANSLATION_ENABLED')) define('ONXSHOP_SIMPLE_TRANSLATION_ENABLED', false);

/**
 * Set pre-action list as array, used in bootstrap.php
 */

$onxshop_pre_actions = array("autologin", "locales");
if (ONXSHOP_ALLOW_SCHEDULER) $onxshop_pre_actions[] = "scheduler";
if (ONXSHOP_FACEBOOK_WITHIN_APP) $onxshop_pre_actions[] = 'component/client/facebook_auth';
