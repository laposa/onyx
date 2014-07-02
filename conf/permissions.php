<?php
/**
 *
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 	
 */

/**
 * General Permissions
 */

define("ONXSHOP_PERMISSION_FRONT_END_EDITING", 1000);

/**
 * Permissions for Back Office Sections
 */

define("ONXSHOP_PERMISSION_PAGES_SECTION", 2000);
define("ONXSHOP_PERMISSION_NEWS_SECTION", 2001);
define("ONXSHOP_PERMISSION_PRODUCTS_SECTION", 2002);
define("ONXSHOP_PERMISSION_RECIPES_SECTION", 2003);
define("ONXSHOP_PERMISSION_STORES_SECTION", 2004);
define("ONXSHOP_PERMISSION_ORDERS_SECTION", 2005);
define("ONXSHOP_PERMISSION_STOCK_SECTION", 2006);
define("ONXSHOP_PERMISSION_CUSTOMERS_SECTION", 2007);
define("ONXSHOP_PERMISSION_STATS_SECTION", 2008);
define("ONXSHOP_PERMISSION_MARKETING_SECTION", 2009);
define("ONXSHOP_PERMISSION_COMMENTS_SECTION", 2010);
define("ONXSHOP_PERMISSION_SURVEYS_SECTION", 2011);
define("ONXSHOP_PERMISSION_ADVANCED_SECTION", 2012);

/**
 * Human Readable Names
 */

$permissions = array(

	ONXSHOP_PERMISSION_FRONT_END_EDITING => "Allow front end editing",

	ONXSHOP_PERMISSION_PAGES_SECTION => "Allow access to Pages section",
	ONXSHOP_PERMISSION_NEWS_SECTION => "Allow access to News section",
	ONXSHOP_PERMISSION_PRODUCTS_SECTION => "Allow access to Products section",
	ONXSHOP_PERMISSION_RECIPES_SECTION => "Allow access to Recipes section",
	ONXSHOP_PERMISSION_STORES_SECTION => "Allow access to Stores section",
	ONXSHOP_PERMISSION_ORDERS_SECTION => "Allow access to Orders section",
	ONXSHOP_PERMISSION_STOCK_SECTION => "Allow access to Stock section",
	ONXSHOP_PERMISSION_CUSTOMERS_SECTION => "Allow access to Customers section",
	ONXSHOP_PERMISSION_STATS_SECTION => "Allow access to Stats section",
	ONXSHOP_PERMISSION_MARKETING_SECTION => "Allow access to Marketing section",
	ONXSHOP_PERMISSION_COMMENTS_SECTION => "Allow access to Comments section",
	ONXSHOP_PERMISSION_SURVEYS_SECTION => "Allow access to Surveys section",
	ONXSHOP_PERMISSION_ADVANCED_SECTION => "Allow access to Advanced section"

);

/**
 * Local extensions
 */

if (file_exists(ONXSHOP_PROJECT_DIR . "conf/permissions.php")) {
	require_once(ONXSHOP_PROJECT_DIR . "conf/permissions.php");
}
