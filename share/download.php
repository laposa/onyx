<?php
/**
 * Copyright (c) 2006-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * This script is initiating sessiona as it's doing ACL check
 *
 * Input variables are:
 * $_GET['file']
 * 
 */

/**
 * Detect working directory and include config file
 */
 
$dir = str_replace($_SERVER["SCRIPT_NAME"], "", $_SERVER["SCRIPT_FILENAME"]);

require_once("$dir/../conf/global.php");

/**
 * set time limit to 1 day
 * it was previously 0 (disabled), but 1 day should be enought and it's to prevent hanging sessions
 */
 
set_time_limit(86400);

/**
 * Set include paths
 */

set_include_path(ONXSHOP_PROJECT_DIR . PATH_SEPARATOR . ONXSHOP_DIR . PATH_SEPARATOR . ONXSHOP_DIR . 'lib/' . PATH_SEPARATOR . get_include_path());
require_once('lib/onxshop.functions.php');

/**
 * Include Bootstrap
 */

require_once("lib/onxshop.bootstrap.php");

/**
 * Init Bootstrap
 */

$Bootstrap = new Onxshop_Bootstrap();

/**
 * Get input and set file path
 */
 
if (isset($_GET['file'])) {
    $file = $_GET['file'];
} else {
    $file = "public_html/share/images/missing_image.png";
}

$file = ONXSHOP_PROJECT_DIR . $file;

$realpath = realpath($file);

/**
 * Read file
 */
 
if (!is_readable($file)) {
    
    //file does not exists
    header("HTTP/1.0 404 Not Found");
    echo "missing";
    
} else {
    
    onxshopCheckForAllowedPath($realpath);

    /**
     * Detect file type and send to the client
     */
    
    $mimetype = mime_content_type($file);
    header("Content-type: $mimetype");
    
    /**
     * tell the client to initiate download dialog
     */
     
    header('Pragma: private');
    header('Cache-control: private, must-revalidate');
    header('Content-Disposition: attachment; filename='.basename($file));
    
    /**
     * Clean (erase) the output buffer and turn off output buffering
     */
     
    ob_end_clean();
    
    /**
     * user rangeDownload function for any client that supports byte-ranges (i.e. Safari browser)
     * 
     */
     
    if (isset($_SERVER['HTTP_RANGE'])) {
        rangeDownload($file);
    } else {
        header("Content-Length: " . filesize($file));
        readfile($file);
    }
    
    session_write_close();
    exit;
}
