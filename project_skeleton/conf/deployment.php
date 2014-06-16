<?php
/**
 * Global deployment configuration
 *
 * Copyright (c) 2009-2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

/**
 * Application debug settings
 *
 * 0 - No debugging
 * 1 - Basic debugging
 * 5 - Full debugging
 *
 * to enable direct debugging use:
 * define('ONXSHOP_DEBUG_DIRECT', true);
 * for more options look in onxshop_dir/conf/global.php
 */

$debug_hosts = array(
'x93.97.247.102' => 1,
'x188.220.10.46' => 1
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
define('ONXSHOP_DB_HOST', 'localhost');
define('ONXSHOP_DB_PORT', 5432);
define('ONXSHOP_DB_NAME', '');

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

/**
 * Salt used for encryption/hashing
 */

if (!defined('ONXSHOP_ENCRYPTION_SALT'))  define('ONXSHOP_ENCRYPTION_SALT', '');
