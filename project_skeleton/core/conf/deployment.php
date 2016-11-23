<?php
/**
 * Deployment configuration
 *
 * Copyright (c) 2009-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * This is configuration specific to the environment, i.e. development or production environment
 * For all options and default values look into onxshop_dir/conf/global.php
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
 * 
 */

$debug_hosts = array(
'x10.0.0.1' => 1,
'x192.168.0.1' => 1
);

/**
 * Force using only one domain
 */

//define('ONXSHOP_MAIN_DOMAIN', 'default.co.uk');

/**
 * Force HTTPS to all pages
 */

//define('ONXSHOP_SSL', true);

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
 * Salt used for encryption/hashing
 */

define('ONXSHOP_ENCRYPTION_SALT', '');
