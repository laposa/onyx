<?php
/** 
 * Copyright (c) 2005-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * Allows to view image file in var/files directory without initiating a session
 *
 * Input variables are:
 * $_GET['image']
 *
 */

/**
 * conditional get PHP implementation written by Simon Willison
 * see http://fishbowl.pastiche.org/archives/001132.html
 *
 * @param unknown_type $timestamp
 */
 
function doConditionalGet($timestamp) {

    $last_modified = substr(date('r', $timestamp), 0, -5).'GMT';
    $etag = '"'.md5($last_modified).'"';
    
    // Send the headers
    header("Last-Modified: $last_modified");
    header("ETag: $etag");
    
    // See if the client has provided the required headers
    $if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ?
        stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) :
        false;
    $if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ?
        stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : 
        false;
    if (!$if_modified_since && !$if_none_match) {
        return;
    }
    
    // At least one of the headers is there - check them
    if ($if_none_match && $if_none_match != $etag) {
        return; // etag is there but doesn't match
    }
    if ($if_modified_since && $if_modified_since != $last_modified) {
        return; // if-modified-since is there but doesn't match
    }
    
    // Nothing has changed since their last request - serve a 304 and exit
    header('HTTP/1.0 304 Not Modified');
    exit;
}


/**
 * set working dir
 */

$dir = str_replace($_SERVER["SCRIPT_NAME"], "", $_SERVER["SCRIPT_FILENAME"]);

require_once("$dir/../conf/global.php");

/**
 * read input and set paths
 */
 
if (!isset($_GET['image'])) {
    $missing = 1;
} else {
    $image_file = $_GET['image'];
    $missing = 0;
}

$image_file = ONYX_PROJECT_DIR . $image_file;

$realpath = realpath($image_file);

if ($realpath == false) $missing = 1;

/**
 * security check
 * it's allowed to see only content of var/ directory
 */
 
if (!$missing) onyxCheckForAllowedPath($realpath, false);

/**
 * 404 check
 */
 
if (!is_readable($image_file) || $missing) {
    //file does not exists
    $image_file = ONYX_PROJECT_DIR . ONYX_MISSING_IMAGE;
    header("HTTP/1.0 404 Not Found");
}

/**
 * read file info
 */

$image_type_mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $image_file);
$modified = filemtime($image_file);
$length = filesize($image_file);

/**
 * send to the client
 */
 
if ($image_type_mime) {
    header("Content-type: " . $image_type_mime);
    header("Content-Length: " . $length);
    doConditionalGet($modified);
    readfile($image_file);
} else {
    header("HTTP/1.0 403 Forbidden");
    echo "not an image";
}
