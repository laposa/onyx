<?php
/**
 * Default Global Onyx configuration
 *
 * Copyright (c) 2005-2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * The constants defined here can be "overwritten" in project_dir/conf/global.php
 * i.e. the configuration from project_dir is parsed before the configuration
 * here in the onyx
 */

 use Symfony\Component\HttpFoundation\IpUtils;

 /**
 * onyxGlobalConfSetValue
 * @param $name constant name
 * @param $value
 */

function onyxGlobalConfSetValue($name, $value) {

    if (defined($name)) {
        return false; // already set
    }

    // check env variable
    if (strlen(getenv($name)) > 0) define($name, getenv($name));
    else define($name, $value);
}

/**
 * can be used as GET parameter when loading resources in browser
 * to force an updated version
 */

onyxGlobalConfSetValue('ONYX_CACHE_VERSION', '2.0.pre1');

/**
 * Force using only one domain (in Onyx_Controller_Uri_Mapping)
 */

onyxGlobalConfSetValue('ONYX_MAIN_DOMAIN', false);

/**
 * HTTP client IP
 */

if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) $http_client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
else $http_client_ip = $_SERVER["REMOTE_ADDR"];

/**
 * Application debug settings
 *
 * 0 - No debugging
 * 1 - Basic debugging
 * 5 - Full debugging
 *
 * to enable direct debugging use:
 * define('ONYX_DEBUG_DIRECT', true);
 *
 */

$debug = false;

$debug_whitelist = explode(',', getenv('ONYX_DEBUG_CIDR_WHITELIST'));

if (is_array($debug_whitelist)) {
   
    foreach($debug_whitelist as $cidr) {
        if (IpUtils::checkIp($http_client_ip, $cidr)) {
            $debug = true;
        }
    }
}

/**
 * Can the remote host see debugging messages?
 * see lib/onyx.functions.php: msg() function for documentation
 */

if($debug)  {
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
    ini_set('display_errors', 1);
    define('ONYX_DEBUG_LEVEL', getenv('ONYX_DEBUG_LEVEL'));
    define('ONYX_DEBUG_INCLUDE_BACKTRACE', true);
    define('ONYX_DEBUG_INCLUDE_USER_ID', true);
    define('ONYX_IS_DEBUG_HOST', true);
    define('ONYX_DEBUG_OUTPUT_SESSION', false); // save in session and manage output on each controller/template level
    define('ONYX_DEBUG_OUTPUT_DIRECT', false); // sends directly to client
    define('ONYX_DEBUG_OUTPUT_FILE', false); // store in var/log/messages/
    define('ONYX_DEBUG_OUTPUT_ERROR_LOG', true); // use Apache error log, i.e. /var/log/apache2/error.log
    define('ONYX_BENCHMARK', false);
    define('ONYX_DB_PROFILER', false);
    define('ONYX_ERROR_EMAIL', null);
    define('ONYX_TRACY', true);
    define('ONYX_TRACY_BENCHMARK', true); // only effective if ONYX_TRACY is true
    define('ONYX_TRACY_DB_PROFILER', true); // only effective if ONYX_TRACY is true
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED & ~E_WARNING);
    ini_set('display_errors', 0);
    define('ONYX_DEBUG_LEVEL', 0);
    define('ONYX_DEBUG_INCLUDE_BACKTRACE', false);
    define('ONYX_DEBUG_INCLUDE_USER_ID', false);
    define('ONYX_IS_DEBUG_HOST', false);
    define('ONYX_DEBUG_OUTPUT_SESSION', false);
    define('ONYX_DEBUG_OUTPUT_DIRECT', false);
    define('ONYX_DEBUG_OUTPUT_FILE', false);
    define('ONYX_DEBUG_OUTPUT_ERROR_LOG', false);
    define('ONYX_BENCHMARK', false);
    define('ONYX_DB_PROFILER', false);
    define('ONYX_ERROR_EMAIL', null);
    define('ONYX_TRACY', false);
    define('ONYX_TRACY_BENCHMARK', false);
    define('ONYX_TRACY_DB_PROFILER', false);
}

/**
 * restrict access to backoffice from listed source range
 * comma separated CIDR notation, example: '192.168.1.1,10.0.0.0/16'
 */

onyxGlobalConfSetValue('ONYX_AUTH_CIDR_WHITELIST', false);

/**
 * Authentication type for backend users
 */

if (!defined('ONYX_AUTH_TYPE')) {
    if (ONYX_DB_TYPE == 'mysql') define('ONYX_AUTH_TYPE', 'mysql');
    else define('ONYX_AUTH_TYPE', 'postgresql');
}
onyxGlobalConfSetValue('ONYX_AUTH_SERVER', ONYX_DB_HOST);

/**
 * HardCoded user/password for backend users
 *
 */

//onyxGlobalConfSetValue('ONYX_EDITOR_USERNAME', 'site_editor_username');
//onyxGlobalConfSetValue('ONYX_EDITOR_PASSWORD', 'site_password_password');

/**
 * Is authentication always required?
 */

onyxGlobalConfSetValue('ONYX_REQUIRE_AUTH', false);

/**
 * Use SSL?
 * will force SSL in Onyx_Controller_Uri_Mapping
 */

onyxGlobalConfSetValue('ONYX_SSL', false);

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
onyxGlobalConfSetValue('ONYX_HSTS_TTL', 3600 * 24 * 60);

/**
 * send X-XSS-Protection HTTP header
 */
onyxGlobalConfSetValue('ONYX_XSS_PROTECTION_ENABLE', true);

/**
 * send X-Content-Type-Options HTTP header
 */
onyxGlobalConfSetValue('ONYX_CONTENT_TYPE_OPTIONS_ENABLE', true);

/**
 * Compress output option (not really important when using Apache Deflate module)
 */

onyxGlobalConfSetValue('ONYX_COMPRESS_OUTPUT', 0);


/**
 * Session type (database, file)
 */

onyxGlobalConfSetValue('ONYX_SESSION_TYPE', 'database');
onyxGlobalConfSetValue('ONYX_SESSION_DIRECTORY', ONYX_PROJECT_DIR . 'var/sessions/'); // also used for .lock files

/**
 * Add the httpOnly flag to the cookie, which makes it inaccessible to browser scripting languages such as JavaScript
 * http://php.net/session.cookie-httponly
 */

ini_set( 'session.cookie_httponly', 1 );

/**
 * change from default PHPSESSID, it will be visible in HTTP header Set-Cookie
 */

onyxGlobalConfSetValue('ONYX_SESSION_NAME', 'OnyxSID');

/**
 * start session for all users
 * if false, session will start only if necessary
 */

onyxGlobalConfSetValue('ONYX_SESSION_START_FOR_ALL_USERS', false);

/**
 * Layout settings
 */

onyxGlobalConfSetValue('ONYX_SITE_TEMPLATE', 'node/site/default');

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
onyxGlobalConfSetValue('ONYX_DB_QUERY_CACHE_BACKEND', 'File'); // change of this will change also ONYX_PAGE_CACHE_BACKEND, see below
onyxGlobalConfSetValue('ONYX_DB_QUERY_CACHE_TTL', 3600);
onyxGlobalConfSetValue('ONYX_DB_QUERY_CACHE_DIRECTORY', ONYX_PROJECT_DIR . 'var/cache/');

/*
 * Cache for whole page
 *
 */
onyxGlobalConfSetValue('ONYX_PAGE_CACHE_DIRECTORY', ONYX_DB_QUERY_CACHE_DIRECTORY); // Same as DB cache
onyxGlobalConfSetValue('ONYX_PAGE_CACHE_BACKEND', ONYX_DB_QUERY_CACHE_BACKEND); // Same as DB cache
onyxGlobalConfSetValue('ONYX_PAGE_CACHE_TTL', 86400); // set 0 to disable

/*
 * General usage cache
 *
 */
onyxGlobalConfSetValue('ONYX_GENERAL_CACHE_DIRECTORY', ONYX_DB_QUERY_CACHE_DIRECTORY); // Same as DB cache
onyxGlobalConfSetValue('ONYX_GENERAL_CACHE_BACKEND', ONYX_DB_QUERY_CACHE_BACKEND); // Same as DB cache
onyxGlobalConfSetValue('ONYX_GENERAL_CACHE_TTL', 3600); // set 0 to disable

/**
 * Libmemcached configuration
 */
onyxGlobalConfSetValue('ONYX_CACHE_BACKEND_LIBMEMCACHED_HOST', 'localhost');
onyxGlobalConfSetValue('ONYX_CACHE_BACKEND_LIBMEMCACHED_PORT', 11211);


/**
 * Onyx package name
 *
 * basic - CMS only
 * standard - CMS + eCommerce
 * enterprise - CMS + eCommerce + custom licence
 */

onyxGlobalConfSetValue('ONYX_PACKAGE_NAME', 'basic');

/**
 * enable ecommerce
 *
 * enable the product table, including everyting connected to it,
 * including orders, invoices and also recipes
 */

onyxGlobalConfSetValue('ONYX_ECOMMERCE', false);

/**
 * enable/disable static file generator
 *
 * allow to use a separate build&publish worklow
 */

onyxGlobalConfSetValue('ONYX_STATIC_FILE_GENERATOR', false);


/**
 * Onyx system support email
 */

onyxGlobalConfSetValue('ONYX_SUPPORT_EMAIL', 'support@laposa.ie');
onyxGlobalConfSetValue('ONYX_SUPPORT_URL', 'mailto:' . ONYX_SUPPORT_EMAIL);
onyxGlobalConfSetValue('ONYX_SUPPORT_NAME', 'Onyx support team');


/**
 * allow backup download
 */

onyxGlobalConfSetValue('ONYX_ALLOW_BACKUP_DOWNLOAD', true);

/**
 * forwarding URLs
 */

onyxGlobalConfSetValue('AFTER_CLIENT_LOGOUT_URL', '/');

/**
 * allow scheduler
 * if not enabled for all, enable for 2% requests
 */

if (!defined('ONYX_ALLOW_SCHEDULER') && (rand(1, 50) == 1)) define('ONYX_ALLOW_SCHEDULER', true);
else define('ONYX_ALLOW_SCHEDULER', false);

/**
 * allow search index update on cache save event
 */

onyxGlobalConfSetValue('ONYX_ALLOW_SEARCH_INDEX_AUTOUPDATE', false);


/**
 * pagination
 */

onyxGlobalConfSetValue('ONYX_PAGINATION_SHOW_ITEMS', 10);

/**
 * Salt used for encryption/hashing
 */

onyxGlobalConfSetValue('ONYX_ENCRYPTION_SALT', '');

/**
 * enable A/B testing
 */

onyxGlobalConfSetValue('ONYX_ENABLE_AB_TESTING', false);

/**
 * CDN for images
 *
 * ONYX_CDN - use CDN (true/false)
 * ONYX_CDN_HOST - static content service node URL
 * ONYX_CDN_USE_WHEN_SSL - use CDN when request is served on SSL (true/false)
 * ONYX_CDN_ALLOWED_CONTEXT - comma seperated list of html tags (img, link, a, script, style)
 * ONYX_CDN_ALLOWED_TYPES - comma seperated list of file extensions (e.g. 'jpg, gif, png')
 */

onyxGlobalConfSetValue('ONYX_CDN', false);
onyxGlobalConfSetValue('ONYX_CDN_HOST', 'http://static-image.server.my');
onyxGlobalConfSetValue('ONYX_CDN_USE_WHEN_SSL', false);
onyxGlobalConfSetValue('ONYX_CDN_ALLOWED_CONTEXT', 'img');
onyxGlobalConfSetValue('ONYX_CDN_ALLOWED_TYPES', 'jpg, gif, png');

/**
 * recaptcha
 */

onyxGlobalConfSetValue('ONYX_RECAPTCHA_PUBLIC_KEY', '');
onyxGlobalConfSetValue('ONYX_RECAPTCHA_PRIVATE_KEY', '');
onyxGlobalConfSetValue('ONYX_RECAPTCHA_MIN_SCORE', 0.5);

/**
 * is backoffice user account required?
 * deprecated since Onyx 1.7
 */

onyxGlobalConfSetValue('ONYX_BACKOFFICE_REQUIRE_CUSTOMER_LOGIN', false);

/**
 * allow to upload files to root folder in media library?
 */

onyxGlobalConfSetValue('ONYX_MEDIA_LIBRARY_ROOT_UPLOAD', false);

/**
 * mark object as invalid when using null on required attributes?
 */

onyxGlobalConfSetValue('ONYX_MODEL_STRICT_VALIDATION', false);

/**
 * allow to merge newly registred account with previous one
 */

onyxGlobalConfSetValue('ONYX_CUSTOMER_ALLOW_ACCOUNT_MERGE', false);

/**
 * simple translation (output regex filter)
 */

onyxGlobalConfSetValue('ONYX_SIMPLE_TRANSLATION_ENABLED', false);

/**
 * Set pre-action list as array, used in bootstrap.php
 */

$onyx_pre_actions = array("locales");
if (ONYX_ALLOW_SCHEDULER) $onyx_pre_actions[] = "scheduler";

/**
 * CSRF protection
 */

onyxGlobalConfSetValue('ONYX_CSRF_PROTECTION_ENABLED', true);

/**
 * Google Map Api Key
 */

onyxGlobalConfSetValue('ONYX_GOOGLE_API_KEY', '');

/**
 * Allow Template editing
 */

onyxGlobalConfSetValue('ONYX_ALLOW_TEMPLATE_EDITING', false);

/**
 * Flick API key
 */
onyxGlobalConfSetValue('ONYX_FLICKR_API_KEY', '');

/**
 * Allow other directory than default ONYX_PROJECT_DIR
 * it's useful for sharing media library across multiple projects
 */

onyxGlobalConfSetValue('ONYX_PROJECT_EXTERNAL_DIRECTORIES', '');

/**
 * Token for permanent login
 */

onyxGlobalConfSetValue('ONYX_TOKEN_NAME', 'onyx_token');

/**
 * Fallback to missing image
 * e.g. when Media Library is not available
 */

onyxGlobalConfSetValue('ONYX_MISSING_IMAGE', 'public_html/share/images/missing_image.png');

/**
 * Mobile App Deeplinks
 */
onyxGlobalConfSetValue('ONYX_MOBILE_APP_SCHEMA', false);
onyxGlobalConfSetValue('ONYX_MOBILE_APP_APP_STORE_ID', false);
onyxGlobalConfSetValue('ONYX_MOBILE_APP_NAME', false);
onyxGlobalConfSetValue('ONYX_MOBILE_APP_PACKAGE', false);
onyxGlobalConfSetValue('ONYX_MOBILE_APP_DEEPLINK_SET_URL', false);
onyxGlobalConfSetValue('ONYX_MOBILE_APP_DEEPLINK_VALIDATION_REGEX', false);
onyxGlobalConfSetValue('ONYX_MOBILE_APP_ASSOCIATED_HOSTNAME', false);
