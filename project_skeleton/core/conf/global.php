<?php
/**
 * Global configuration
 *
 * Copyright (c) 2009-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * This is configuration shared between development and production environment
 * For all options look in onxshop_dir/conf/global.php
 *
 */

/**
 * Server specific configuration (i.e. different on dev&live server)
 */

require_once('deployment.php');

/**
 * Project specific configuration (i.e. shared on dev&live server)
 * See onxshop_dir/conf/global.php for all options
 */
 
//define('ONXSHOP_MAIN_TEMPLATE','node/site/default');

/**
 * enable ecommerce
 * 
 * enable the product table, including everything connected to it,
 * i.e. orders, invoices and also recipes, stores
 */
 
define('ONXSHOP_ECOMMERCE', true);

/**
 * Include default global configuration
 */

require_once(ONXSHOP_DIR . "conf/global.php");

