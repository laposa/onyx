<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
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
 * Set include paths
 */
 
set_include_path(get_include_path() . PATH_SEPARATOR . ONXSHOP_DIR);

/**
 * read input and set paths
 */
 
if (!isset($_GET['image'])) {
	$missing = 1;
} else {
	$image = $_GET['image'];
}

$image = ONXSHOP_PROJECT_DIR . $image;

$realpath = realpath($image);

if ($realpath == false) $missing = 1;

/**
 * security check
 * it's allowed to see only content of var/ directory
 */
 
$check = addcslashes(ONXSHOP_PROJECT_DIR, '/') . 'var\/';

if (!preg_match("/$check/", $realpath) && !$missing) {
	header("HTTP/1.0 403 Forbidden");
	echo " forbidden!";
	exit;
}

if (!is_readable($image) || $missing) {
	//file does not exists
	$image = ONXSHOP_PROJECT_DIR . "public_html/share/images/missing_image.png";
	header("HTTP/1.0 404 Not Found");
	// log it
}

/**
 * read file info
 */
 
$image_type = exif_imagetype($image);
$modified = filemtime($image);
$length = filesize($image);

/**
 * send to the client
 */
 
if ($image_type) {
	header("Content-type: " . image_type_to_mime_type($image_type));
	header("Content-Length: " . $length);
	doConditionalGet($modified);
	readfile($image);
} else {
	//file is not image, attempt to hack?
	header("HTTP/1.0 403 Forbidden");
	echo "not an image";
	// TODO: log it
}

