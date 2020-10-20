<?php
/**
 * Default Global Onyx configuration
 *
 * Copyright (c) 2005-2020 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * The constants defined here can be "overwritten" in project_dir/conf/global.php
 * i.e. the configuration from project_dir is parsed before the configuration
 * here in the onyx
 */

/**
 * can be used as GET parameter in loading resources in browser
 * to for loading an updated version
 */

define('ONYX_CACHE_VERSION', '20');

/**
 * Force using only one domain (in Onyx_Controller_Uri_Mapping)
 */

//define('ONYX_MAIN_DOMAIN', 'default.co.uk');

/**
 * HTTP client IP
 */
 
if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) $http_client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
else $http_client_ip = $_SERVER["REMOTE_ADDR"];

/**
 * Can the remote host see debugging messages?
 * see lib/onyx.functions.php: msg() function for documentation
 */

if(in_array($http_client_ip, array_keys($debug_hosts)))  {
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
    ini_set('display_errors', 1);
    define('ONYX_DEBUG_LEVEL', $debug_hosts[$http_client_ip]);
    define('ONYX_DEBUG_INCLUDE_BACKTRACE', true);
    define('ONYX_DEBUG_INCLUDE_USER_ID', true);
    define('ONYX_IS_DEBUG_HOST', true);
    define('ONYX_DEBUG_OUTPUT_SESSION', false); // save in session and manage output on each controller/template level
    define('ONYX_DEBUG_OUTPUT_DIRECT', false); // sends directly to client
    define('ONYX_DEBUG_OUTPUT_FILE', false); // store in var/log/messages/
    define('ONYX_DEBUG_OUTPUT_FIREBUG', false); // use Firebug
    define('ONYX_DEBUG_OUTPUT_ERROR_LOG', true); // use Apache error log, i.e. /var/log/apache2/error.log
    define('ONYX_BENCHMARK', false);
    define('ONYX_DB_PROFILER', false);
    define('ONYX_ERROR_EMAIL', null);
    define('ONYX_TRACY', false);
    define('ONYX_TRACY_BENCHMARK', true); // only effective if ONYX_TRACY is true
    define('ONYX_TRACY_DB_PROFILER', false); // only effective if ONYX_TRACY is true
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
    ini_set('display_errors', 0);
    define('ONYX_DEBUG_LEVEL', 0);
    define('ONYX_DEBUG_INCLUDE_BACKTRACE', false);
    define('ONYX_DEBUG_INCLUDE_USER_ID', true);
    define('ONYX_IS_DEBUG_HOST', false);
    define('ONYX_DEBUG_OUTPUT_SESSION', false);
    define('ONYX_DEBUG_OUTPUT_DIRECT', false);
    define('ONYX_DEBUG_OUTPUT_FILE', false);
    define('ONYX_DEBUG_OUTPUT_FIREBUG', false);
    define('ONYX_DEBUG_OUTPUT_ERROR_LOG', true);
    define('ONYX_BENCHMARK', false);
    define('ONYX_DB_PROFILER', false);
    define('ONYX_ERROR_EMAIL', null);
    define('ONYX_TRACY', false);
    define('ONYX_TRACY_BENCHMARK', true);
    define('ONYX_TRACY_DB_PROFILER', true);
}

/**
 * Authentication type for backend users
 */

if (!defined('ONYX_AUTH_TYPE')) {
    if (ONYX_DB_TYPE == 'mysql') define('ONYX_AUTH_TYPE', 'mysql');
    else define('ONYX_AUTH_TYPE', 'postgresql');
}
if (!defined('ONYX_AUTH_SERVER')) define('ONYX_AUTH_SERVER', ONYX_DB_HOST);

/**
 * HardCoded user/password for backend users
 *
 */

//if (!defined('ONYX_EDITOR_USERNAME')) define('ONYX_EDITOR_USERNAME', 'site_editor_username');
//if (!defined('ONYX_EDITOR_PASSWORD')) define('ONYX_EDITOR_PASSWORD', 'site_password_password');

/**
 * Is authentication always required?
 */

if (!defined('ONYX_REQUIRE_AUTH')) define('ONYX_REQUIRE_AUTH', false);

/**
 * Use SSL?
 * will force SSL in Onyx_Controller_Uri_Mapping
 */

if (!defined('ONYX_SSL')) define('ONYX_SSL', false);

if (!defined('ONYX_EDITOR_USE_SSL')) {
    if (ONYX_SSL) define('ONYX_EDITOR_USE_SSL', true);
    else define('ONYX_EDITOR_USE_SSL', false);
}
if (!defined('ONYX_CUSTOMER_USE_SSL')) {
    if (ONYX_SSL) define('ONYX_CUSTOMER_USE_SSL', true);
    else define('ONYX_CUSTOMER_USE_SSL', false);
}
if (!defined('ONYX_HSTS_ENABLE')) {
    if (ONYX_SSL) define('ONYX_HSTS_ENABLE', true);
    else define('ONYX_HSTS_ENABLE', false);
}    
if (!defined('ONYX_HSTS_TTL')) define('ONYX_HSTS_TTL', 3600 * 24 * 60);

/**
 * send X-XSS-Protection HTTP header
 */
if (!defined('ONYX_XSS_PROTECTION_ENABLE')) define('ONYX_XSS_PROTECTION_ENABLE', true);

/**
 * send X-Content-Type-Options HTTP header
 */
if (!defined('ONYX_CONTENT_TYPE_OPTIONS_ENABLE')) define('ONYX_CONTENT_TYPE_OPTIONS_ENABLE', true);

/**
 * Compress output option (not really important when using Apache Deflate module)
 */
 
if (!defined('ONYX_COMPRESS_OUTPUT')) define('ONYX_COMPRESS_OUTPUT', 0);


/**
 * Session type (database, file)
 */

if (!defined('ONYX_SESSION_TYPE')) define('ONYX_SESSION_TYPE', 'database');
if (!defined('ONYX_SESSION_DIRECTORY')) define('ONYX_SESSION_DIRECTORY', ONYX_PROJECT_DIR . 'var/sessions/'); // also used for .lock files

/**
 * Add the httpOnly flag to the cookie, which makes it inaccessible to browser scripting languages such as JavaScript
 * http://php.net/session.cookie-httponly
 */
 
ini_set( 'session.cookie_httponly', 1 );

/**
 * change from default PHPSESSID, it will be visible in HTTP header Set-Cookie
 */

if (!defined('ONYX_SESSION_NAME')) define('ONYX_SESSION_NAME', 'OnyxSID');

/**
 * start session for all users
 * if false, session will start only if necessary
 */

if (!defined('ONYX_SESSION_START_FOR_ALL_USERS')) define('ONYX_SESSION_START_FOR_ALL_USERS', true);

/**
 * Layout settings
 */
 
if (!defined('ONYX_DEFAULT_TYPE')) define('ONYX_DEFAULT_TYPE', 'sys/html5');
if (!defined('ONYX_MAIN_TEMPLATE')) define('ONYX_MAIN_TEMPLATE', 'node/site/default');
if (!defined('ONYX_PAGE_TEMPLATE')) define('ONYX_PAGE_TEMPLATE', 'node/page/default');
if (!defined('ONYX_DEFAULT_LAYOUT')) define('ONYX_DEFAULT_LAYOUT', ONYX_DEFAULT_TYPE . '.' . ONYX_MAIN_TEMPLATE);


//hack
if (isset($_GET['preview']) && $_GET['preview'] == 1) {
    $_SESSION['preview'] = 1;
} else if (isset($_GET['exit_preview']) && $_GET['exit_preview'] == 1) {
    $_SESSION['preview'] = 0;
}

/**
 * detect if any backoffice controller is called
 */
if (preg_match("/^\/(backoffice|request\/bo)\//", $_GET['translate'])) define('ONYX_IN_BACKOFFICE', true);
else define('ONYX_IN_BACKOFFICE', false);

/**
 * cache backend possible values: File, Apc, Libmemcached
 * Shared hosting should be using only File backend
 * Apc and Libmemcached should be used only on dedicated servers
 */
 
if (!defined('ONYX_DB_QUERY_CACHE_BACKEND')) define('ONYX_DB_QUERY_CACHE_BACKEND', 'File'); // change of this will change also ONYX_PAGE_CACHE_BACKEND, see below
if (!defined('ONYX_DB_QUERY_CACHE_TTL')) define('ONYX_DB_QUERY_CACHE_TTL', 3600);
if (!defined('ONYX_DB_QUERY_CACHE_DIRECTORY')) define('ONYX_DB_QUERY_CACHE_DIRECTORY', ONYX_PROJECT_DIR . 'var/cache/');

/*
 * Zend Cache for whole page
 * 
 */
 
if (!defined('ONYX_PAGE_CACHE_DIRECTORY')) define('ONYX_PAGE_CACHE_DIRECTORY', ONYX_DB_QUERY_CACHE_DIRECTORY); // Same as DB cache
if (!defined('ONYX_PAGE_CACHE_BACKEND')) define('ONYX_PAGE_CACHE_BACKEND', ONYX_DB_QUERY_CACHE_BACKEND); // Same as DB cache
if (!defined('ONYX_PAGE_CACHE_TTL')) define('ONYX_PAGE_CACHE_TTL', 86400); // set 0 to disable

/**
 * Libmemcached configuration
 */

if (!defined('ONYX_CACHE_BACKEND_LIBMEMCACHED_HOST')) define('ONYX_CACHE_BACKEND_LIBMEMCACHED_HOST', 'localhost');
if (!defined('ONYX_CACHE_BACKEND_LIBMEMCACHED_PORT')) define('ONYX_CACHE_BACKEND_LIBMEMCACHED_PORT', 11211);



/**
 * Onyx package name
 * 
 * basic - CMS only
 * standard - CMS + eCommerce
 * enterprise - CMS + eCommerce + custom licence
 */

if (!defined('ONYX_PACKAGE_NAME')) define('ONYX_PACKAGE_NAME', 'basic');

/**
 * enable ecommerce
 * 
 * enable the product table, including everyting connected to it,
 * including orders, invoices and also recipes
 */
 
if (!defined('ONYX_ECOMMERCE')) define('ONYX_ECOMMERCE', false);

/**
 * enable/disable static file generator
 *
 * allow to use a separate build&publish worklow
 */

if (!defined('ONYX_STATIC_FILE_GENERATOR')) define('ONYX_STATIC_FILE_GENERATOR', false);


/**
 * Onyx system support email
 */

if (!defined('ONYX_SUPPORT_WEBSITE_URL')) define('ONYX_SUPPORT_WEBSITE_URL', 'https://onxshop.com/support');
if (!defined('ONYX_SUPPORT_EMAIL')) define('ONYX_SUPPORT_EMAIL', 'support@onxshop.com');
if (!defined('ONYX_SUPPORT_NAME')) define('ONYX_SUPPORT_NAME', 'Onyx support team');


/**
 * allow backup download
 */

if (!defined('ONYX_ALLOW_BACKUP_DOWNLOAD')) define('ONYX_ALLOW_BACKUP_DOWNLOAD', true);

/**
 * forwarding URLs
 */
 
if (!defined('BASKET_CONTINUE_SHOPPING_URL')) define('BASKET_CONTINUE_SHOPPING_URL', '/');
if (!defined('AFTER_CLIENT_LOGOUT_URL')) define('AFTER_CLIENT_LOGOUT_URL', '/');

/**
 * allow scheduler
 */

if (!defined('ONYX_ALLOW_SCHEDULER')) define('ONYX_ALLOW_SCHEDULER', true);

/**
 * allow search index update on cache save event
 */

if (!defined('ONYX_ALLOW_SEARCH_INDEX_AUTOUPDATE')) define('ONYX_ALLOW_SEARCH_INDEX_AUTOUPDATE', false);

/**
 * social: Facebook
 */
 
if (!defined('ONYX_FACEBOOK_APP_ID')) define('ONYX_FACEBOOK_APP_ID', 0);
if (!defined('ONYX_FACEBOOK_APP_SECRET')) define('ONYX_FACEBOOK_APP_SECRET', '');
if (!defined('ONYX_FACEBOOK_APP_NAMESPACE')) define('ONYX_FACEBOOK_APP_NAMESPACE', '');
if (!defined('ONYX_FACEBOOK_APP_OG_STORIES')) define('ONYX_FACEBOOK_APP_OG_STORIES', '');
if (!defined('ONYX_FACEBOOK_OG_IMAGE')) define('ONYX_FACEBOOK_OG_IMAGE', 'var/files/favicon.ico');
// Canvas Facebook Page is defined by APP_NAMESPACE
define('ONYX_FACEBOOK_CANVAS_PAGE', 'https://apps.facebook.com/' . ONYX_FACEBOOK_APP_NAMESPACE);
// determine environment (desktop vs mobile)
if (($_POST['signed_request'] && $_POST['fb_locale'])) define('ONYX_FACEBOOK_ENV', 'desktop');
else if ($_GET['ref'] == 'web_canvas') define('ONYX_FACEBOOK_ENV', 'mobile');
else define('ONYX_FACEBOOK_ENV', '');
// determine if we are running under Facebook App
if (ONYX_FACEBOOK_ENV == 'desktop' || ONYX_FACEBOOK_ENV == 'mobile') define('ONYX_FACEBOOK_WITHIN_APP', true);
else define('ONYX_FACEBOOK_WITHIN_APP', false);
if (ONYX_FACEBOOK_WITHIN_APP) define('ONYX_FACEBOOK_AUTH', true);
else define('ONYX_FACEBOOK_AUTH', false);

/**
 * social: Twitter
 */
 
if (!defined('ONYX_TWITTER_APP_ID')) define('ONYX_TWITTER_APP_ID', '');
if (!defined('ONYX_TWITTER_APP_SECRET')) define('ONYX_TWITTER_APP_SECRET', '');
if (!defined('ONYX_TWITTER_USERNAME')) define('ONYX_TWITTER_USERNAME', 'onyx');
if (!defined('ONYX_TWITTER_HASHTAG')) define('ONYX_TWITTER_HASHTAG', ''); //without hash
if (!defined('ONYX_TWITTER_ACCESS_TOKEN')) define('ONYX_TWITTER_ACCESS_TOKEN', '');
if (!defined('ONYX_TWITTER_ACCESS_TOKEN_SECRET')) define('ONYX_TWITTER_ACCESS_TOKEN_SECRET', '');

/**
 * pagination
 */
 
if (!defined('ONYX_PAGINATION_SHOW_ITEMS')) define('ONYX_PAGINATION_SHOW_ITEMS', 10);

/**
 * Salt used for encryption/hashing
 */

if (!defined('ONYX_ENCRYPTION_SALT'))  define('ONYX_ENCRYPTION_SALT', '');

/**
 * enable A/B testing
 */

if (!defined('ONYX_ENABLE_AB_TESTING')) define('ONYX_ENABLE_AB_TESTING', false);

/**
 * CDN for images
 *
 * ONYX_CDN - use CDN (true/false)
 * ONYX_CDN_HOST - static content service node URL
 * ONYX_CDN_USE_WHEN_SSL - use CDN when request is served on SSL (true/false)
 * ONYX_CDN_ALLOWED_CONTEXT - comma seperated list of html tags (img, link, a, script, style)
 * ONYX_CDN_ALLOWED_TYPES - comma seperated list of file extensions (e.g. 'jpg, gif, png')
 */

if (!defined('ONYX_CDN')) define('ONYX_CDN', false);
if (!defined('ONYX_CDN_HOST')) define('ONYX_CDN_HOST', 'http://static-image.server.my');
if (!defined('ONYX_CDN_USE_WHEN_SSL')) define('ONYX_CDN_USE_WHEN_SSL', false);
if (!defined('ONYX_CDN_ALLOWED_CONTEXT')) define('ONYX_CDN_ALLOWED_CONTEXT', 'img');
if (!defined('ONYX_CDN_ALLOWED_TYPES')) define('ONYX_CDN_ALLOWED_TYPES', 'jpg, gif, png');

/**
 * recaptcha
 */
 
if (!defined('ONYX_RECAPTCHA_PUBLIC_KEY')) define('ONYX_RECAPTCHA_PUBLIC_KEY', '');
if (!defined('ONYX_RECAPTCHA_PRIVATE_KEY')) define('ONYX_RECAPTCHA_PRIVATE_KEY', '');

/**
 * is backoffice user account required?
 * deprecated since Onyx 1.7
 */

if (!defined('ONYX_BACKOFFICE_REQUIRE_CUSTOMER_LOGIN')) define('ONYX_BACKOFFICE_REQUIRE_CUSTOMER_LOGIN', false);

/**
 * allow to upload files to root folder in media library?
 */

if (!defined('ONYX_MEDIA_LIBRARY_ROOT_UPLOAD')) define('ONYX_MEDIA_LIBRARY_ROOT_UPLOAD', false);

/**
 * mark object as invalid when using null on required attributes?
 */

if (!defined('ONYX_MODEL_STRICT_VALIDATION')) define('ONYX_MODEL_STRICT_VALIDATION', false);

/**
 * allow to merge newly registred account with previous one
 */

if (!defined('ONYX_CUSTOMER_ALLOW_ACCOUNT_MERGE')) define('ONYX_CUSTOMER_ALLOW_ACCOUNT_MERGE', false);

/**
 * simple translation (output regex filter)
 */

if (!defined('ONYX_SIMPLE_TRANSLATION_ENABLED')) define('ONYX_SIMPLE_TRANSLATION_ENABLED', false);

/**
 * Set pre-action list as array, used in bootstrap.php
 */

$onyx_pre_actions = array("autologin", "locales");
if (ONYX_ALLOW_SCHEDULER) $onyx_pre_actions[] = "scheduler";
if (ONYX_FACEBOOK_AUTH) $onyx_pre_actions[] = 'component/client/facebook_auth';

/**
 * CSRF protection
 */
 
if (!defined('ONYX_CSRF_PROTECTION_ENABLED')) define('ONYX_CSRF_PROTECTION_ENABLED', true);

/**
 * Google Map Api Key
 */

if (!defined('ONYX_GOOGLE_API_KEY')) define('ONYX_GOOGLE_API_KEY', '');

/**
 * Allow Template editing
 */

if (!defined('ONYX_ALLOW_TEMPLATE_EDITING')) define('ONYX_ALLOW_TEMPLATE_EDITING', false);

/**
 * Flick API key
 */
if (!defined('ONYX_FLICKR_API_KEY')) define('ONYX_FLICKR_API_KEY', '');

/**
 * Allow other directory than default ONYX_PROJECT_DIR
 * it's useful for sharing media library across multiple projects
 */
 
if (!defined('ONYX_PROJECT_EXTERNAL_DIRECTORIES')) define('ONYX_PROJECT_EXTERNAL_DIRECTORIES', '');

/**
 * Token for permanent login
 */

if (!defined('ONYX_TOKEN_NAME')) define('ONYX_TOKEN_NAME', 'onyx_token'); 

