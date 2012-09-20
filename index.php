<?php
/**
 * Onxshop index / dispatcher
 *
 * An entry point for all actions except thumbnails and download handlers (share/*.php scripts),
 * see htaccess for more details. 
 *
 * Copyright (c) 2008-2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

/**
 * Include global configuration
 */

require_once('../conf/global.php');

/**
 * Set version
 */

define("ONXSHOP_VERSION", trim(file_get_contents(ONXSHOP_DIR . 'ONXSHOP_VERSION')));

/**
 * Include bootstrap file
 */

require_once(ONXSHOP_DIR . 'bootstrap.php');
