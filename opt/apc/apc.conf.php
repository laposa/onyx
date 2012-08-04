<?php
/** 
 * Copyright (c) 2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

/**
 * detect onxshop project directory
 */
 
$onxshop_project_dir =  preg_replace('/public_html\/opt\/apc/', '', dirname($_SERVER['SCRIPT_FILENAME']));

/**
 * include Onxshop project configuration
 */
 
require_once($onxshop_project_dir . 'conf/global.php');

/**
 * security check: allow to use only by debug IP
 */
 
if (!constant('ONXSHOP_IS_DEBUG_HOST')) {
	echo "Sorry, you are not allowed to use this script. Add your IP address to debug host in conf/deployment.php";
	exit;
}

/**
 * set APC manager username and password same as Onxshop database user
 */
 
defaults('ADMIN_USERNAME', ONXSHOP_DB_USER);
defaults('ADMIN_PASSWORD', ONXSHOP_DB_PASSWORD);