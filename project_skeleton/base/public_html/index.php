<?php
/**
 * Onyx index / dispatcher
 *
 * An entry point for all actions except thumbnails and download handlers (share/*.php scripts),
 * see htaccess for more details. 
 *
 * Copyright (c) 2008-2012 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

/**
 * Set output header
 */

header( 'Content-Type: text/html; charset=UTF-8' );

/**
 * Include global configuration
 */

require_once('../conf/global.php');

/**
 * Set version
 */

define("ONYX_VERSION", trim(file_get_contents(ONYX_DIR . 'ONYX_VERSION')));

/**
 * Include bootstrap file
 */

require_once(ONYX_DIR . 'bootstrap.php');
