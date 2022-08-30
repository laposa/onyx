<?php
/**
 * Copyright (c) 2006-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * Allows to view a file in var/files directory without initiating a session
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
 * disable time limit
 */
 
set_time_limit(0);

/**
 * Set include paths
 */

set_include_path(ONYX_PROJECT_DIR . PATH_SEPARATOR . ONYX_DIR . PATH_SEPARATOR . ONYX_DIR . 'lib/' . PATH_SEPARATOR . get_include_path());
require_once('lib/onyx.functions.php');

/**
 * Get input and set file path
 */
 
if (isset($_GET['file'])) {
    $file = $_GET['file'];
} else {
    $file = ONYX_MISSING_IMAGE;
}

$file = ONYX_PROJECT_DIR . $file;

$realpath = realpath($file);

/**
 * Read file
 */
 
if (!is_readable($file)) {
    
    //file does not exists
    header("HTTP/1.0 404 Not Found");
    echo "missing";
    
} else {
    
    /**
     * security check
     */
     
    onyxCheckForAllowedPath($realpath);

    /**
     * Detect file type and send to the client
     */
    
    $mimetype = mime_content_type($file);
    header("Content-type: $mimetype");
    
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
}
