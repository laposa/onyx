<?php
/**
 * Global deployment configuration
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

/**
 * Application Debug settings
 *
 * 0 - No debugging
 * 3 - Full debugging
 * 5 - Full debugging (direct output)
 */

$debug_hosts = array(
'x93.97.247.102' => 1,
'x188.220.10.89' => 1
);

/**
 * Force using only one domain
 */

//define('ONXSHOP_MAIN_DOMAIN', 'default.co.uk');


/**
 * Directory paths
 */

define('ONXSHOP_DIR', realpath(dirname(__FILE__) . '/../onxshop_dir/') . '/');
define('ONXSHOP_PROJECT_DIR', realpath(dirname(__FILE__) . '/../') . '/');


/**
 * Database connection
 */

define('ONXSHOP_DB_TYPE', 'pgsql');
define('ONXSHOP_DB_USER', '');
define('ONXSHOP_DB_PASSWORD', '');
define('ONXSHOP_DB_HOST', 'dbserver');
define('ONXSHOP_DB_PORT', 5432);
define('ONXSHOP_DB_NAME', '');

/**
 * Authentication type for backend users
 */

define('ONXSHOP_AUTH_TYPE', 'postgresql');
define('ONXSHOP_AUTH_SERVER', ONXSHOP_DB_HOST);

/**
 * HardCoded user/password
 *
 */

//define('ONXSHOP_EDITOR_USERNAME', 'site_editor_username');
//define('ONXSHOP_EDITOR_PASSWORD', 'site_password_password');

/**
 * Is authentication always required?
 */

define('ONXSHOP_REQUIRE_AUTH', false);

/**
 * Use SSL?
 */

define('ONXSHOP_EDITOR_USE_SSL', false);
define('ONXSHOP_CUSTOMER_USE_SSL', false);

/**
 * Onxshop package name
 * 
 * (basic, standard, premium)
 * 
 * basic - only CMS
 * standard - CMS + eCommerce
 * premium - CMS + eCommerce + available backup download
 */

//define('ONXSHOP_PACKAGE_NAME', 'standard');
