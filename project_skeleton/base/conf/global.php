<?php
/**
 * Global configuration
 *
 * Copyright (c) 2009-2020 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * This is configuration shared between development and production environment
 * For all options and default values look into onxshop_dir/conf/global.php
 *
 */

/**
 * Include composer packages
 */

require_once(__DIR__ . '/../vendor/autoload.php');

/**
 * Load env variables
 */

if (file_exists(__DIR__ . "/../.env")) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
    $dotenv->load();
}

/**
 * Application debug settings
 *
 * 0 - No debugging
 * 1 - Basic debugging
 * 5 - Full debugging
 *
 * to enable direct debugging use:
 * define('ONXSHOP_DEBUG_DIRECT', true);
 * 
 */

$debug_hosts = array(
'x10.0.0.1' => 1,
'x192.168.0.1' => 1
);

/**
 * Directory paths
 */

define('ONXSHOP_DIR', realpath(dirname(__FILE__) . '/../onyx/') . '/');
define('ONXSHOP_PROJECT_DIR', realpath(dirname(__FILE__) . '/../') . '/');

/**
 * Database connection
 */

define('ONXSHOP_DB_TYPE', 'pgsql');
define('ONXSHOP_DB_USER', getenv('ONYX_DB_USER'));
define('ONXSHOP_DB_PASSWORD', getenv('ONYX_DB_PASSWORD'));
define('ONXSHOP_DB_HOST', getenv('ONYX_DB_HOST'));
define('ONXSHOP_DB_PORT', 5432);
define('ONXSHOP_DB_NAME', getenv('ONYX_DB_NAME'));

/**
 * Salt used for encryption/hashing
 */

define('ONXSHOP_ENCRYPTION_SALT', '');

/**
 * enable ecommerce
 * 
 * enable the product table, including everything connected to it,
 * i.e. orders, invoices and also recipes, stores
 */
 
define('ONXSHOP_ECOMMERCE', false);

/**
 * Include default global configuration
 */

require_once(ONXSHOP_DIR . "conf/global.php");

