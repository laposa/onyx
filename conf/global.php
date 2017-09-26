<?php
/**
 * Default Global Onxshop configuration
 *
 * Copyright (c) 2005-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * The constants defined here can be "overwritten" in project_dir/conf/global.php
 * i.e. the configuration from project_dir is parsed before the configuration
 * here in the onxshop_dir
 */

/**
 * Force using only one domain (in Onxshop_Controller_Uri_Mapping)
 */

//define('ONXSHOP_MAIN_DOMAIN', 'default.co.uk');

/**
 * Can the remote host see debugging messages?
 * see lib/onxshop.functions.php: msg() function for documentation
 */
 
if(in_array($_SERVER["REMOTE_ADDR"], array_keys($debug_hosts)))  {
	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
	ini_set('display_errors', 1);
	define('ONXSHOP_DEBUG_LEVEL', $debug_hosts[$_SERVER["REMOTE_ADDR"]]);
	define('ONXSHOP_DEBUG_INCLUDE_BACKTRACE', true);
	define('ONXSHOP_DEBUG_INCLUDE_USER_ID', true);
	define('ONXSHOP_IS_DEBUG_HOST', true);
	define('ONXSHOP_DEBUG_OUTPUT_SESSION', false); // save in session and manage output on each controller/template level
	define('ONXSHOP_DEBUG_OUTPUT_DIRECT', false); // sends directly to client
	define('ONXSHOP_DEBUG_OUTPUT_FILE', false); // store in var/log/messages/
	define('ONXSHOP_DEBUG_OUTPUT_FIREBUG', false); // use Firebug
	define('ONXSHOP_DEBUG_OUTPUT_ERROR_LOG', true); // use Apache error log, i.e. /var/log/apache2/error.log
	define('ONXSHOP_BENCHMARK', false);
	define('ONXSHOP_DB_PROFILER', false);
	define('ONXSHOP_ERROR_EMAIL', null);
	define('ONXSHOP_TRACY', false);
	define('ONXSHOP_TRACY_BENCHMARK', true); // only effective if ONXSHOP_TRACY is true
	define('ONXSHOP_TRACY_DB_PROFILER', true); // only effective if ONXSHOP_TRACY is true
} else {
	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
	ini_set('display_errors', 0);
	define('ONXSHOP_DEBUG_LEVEL', 0);
	define('ONXSHOP_DEBUG_INCLUDE_BACKTRACE', false);
	define('ONXSHOP_DEBUG_INCLUDE_USER_ID', true);
	define('ONXSHOP_IS_DEBUG_HOST', false);
	define('ONXSHOP_DEBUG_OUTPUT_SESSION', false);
	define('ONXSHOP_DEBUG_OUTPUT_DIRECT', false);
	define('ONXSHOP_DEBUG_OUTPUT_FILE', false);
	define('ONXSHOP_DEBUG_OUTPUT_FIREBUG', false);
	define('ONXSHOP_DEBUG_OUTPUT_ERROR_LOG', true);
	define('ONXSHOP_BENCHMARK', false);
	define('ONXSHOP_DB_PROFILER', false);
	define('ONXSHOP_ERROR_EMAIL', null);
	define('ONXSHOP_TRACY', false);
	define('ONXSHOP_TRACY_BENCHMARK', true);
	define('ONXSHOP_TRACY_DB_PROFILER', true);
}

/**
 * Add the httpOnly flag to the cookie, which makes it inaccessible to browser scripting languages such as JavaScript
 * http://php.net/session.cookie-httponly
 */
 
ini_set( 'session.cookie_httponly', 1 );

/**
 * Authentication type for backend users
 */

if (!defined('ONXSHOP_AUTH_TYPE')) {
    if (ONXSHOP_DB_TYPE == 'mysql') define('ONXSHOP_AUTH_TYPE', 'mysql');
    else define('ONXSHOP_AUTH_TYPE', 'postgresql');
}
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
 * will force SSL in Onxshop_Controller_Uri_Mapping
 */

if (!defined('ONXSHOP_SSL')) define('ONXSHOP_SSL', false);

if (!defined('ONXSHOP_EDITOR_USE_SSL')) {
	if (ONXSHOP_SSL) define('ONXSHOP_EDITOR_USE_SSL', true);
	else define('ONXSHOP_EDITOR_USE_SSL', false);
}
if (!defined('ONXSHOP_CUSTOMER_USE_SSL')) {
	if (ONXSHOP_SSL) define('ONXSHOP_CUSTOMER_USE_SSL', true);
	else define('ONXSHOP_CUSTOMER_USE_SSL', false);
}
if (!defined('ONXSHOP_HSTS_ENABLE')) define('ONXSHOP_HSTS_ENABLE', false);
if (!defined('ONXSHOP_HSTS_TTL')) define('ONXSHOP_HSTS_TTL', 3600);

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
if (preg_match("/^\/(backoffice|request\/bo)\//", $_GET['translate'])) define('ONXSHOP_IN_BACKOFFICE', true);
else define('ONXSHOP_IN_BACKOFFICE', false);

/**
 * cache backend possible values: File, Apc, Libmemcached
 * Shared hosting should be using only File backend
 * Apc and Libmemcached should be used only on dedicated servers
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
 * basic - CMS only
 * standard - CMS + eCommerce
 * enterprise - CMS + eCommerce + custom licence
 */

if (!defined('ONXSHOP_PACKAGE_NAME')) define('ONXSHOP_PACKAGE_NAME', 'basic');

/**
 * enable ecommerce
 * 
 * enable the product table, including everyting connected to it,
 * including orders, invoices and also recipes
 */
 
if (!defined('ONXSHOP_ECOMMERCE')) define('ONXSHOP_ECOMMERCE', false);

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
if (ONXSHOP_FACEBOOK_WITHIN_APP) define('ONXSHOP_FACEBOOK_AUTH', true);
else define('ONXSHOP_FACEBOOK_AUTH', false);

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
 * deprecated since Onxshop 1.7
 */

if (!defined('ONXSHOP_BACKOFFICE_REQUIRE_CUSTOMER_LOGIN')) define('ONXSHOP_BACKOFFICE_REQUIRE_CUSTOMER_LOGIN', false);

/**
 * allow to upload files to root folder in media library?
 */

if (!defined('ONXSHOP_MEDIA_LIBRARY_ROOT_UPLOAD')) define('ONXSHOP_MEDIA_LIBRARY_ROOT_UPLOAD', false);

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
if (ONXSHOP_FACEBOOK_AUTH) $onxshop_pre_actions[] = 'component/client/facebook_auth';

/**
 * CSRF protection
 */
 
if (!defined('ONXSHOP_CSRF_PROTECTION_ENABLED')) define('ONXSHOP_CSRF_PROTECTION_ENABLED', true);

/**
 * Google Map Api Key
 */

if (!defined('ONXSHOP_GOOGLE_API_KEY')) define('ONXSHOP_GOOGLE_API_KEY', '');

/**
 * Allow Template editing
 */

if (!defined('ONXSHOP_ALLOW_TEMPLATE_EDITING')) define('ONXSHOP_ALLOW_TEMPLATE_EDITING', false);

/**
 * Flick API key
 */
if (!defined('ONXSHOP_FLICKR_API_KEY')) define('ONXSHOP_FLICKR_API_KEY', '');

