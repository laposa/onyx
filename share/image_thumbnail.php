<?php
/** 
 * Copyright (c) 2005-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

/**
 * find active directory
 */

$dir = str_replace($_SERVER["SCRIPT_NAME"], "", $_SERVER["SCRIPT_FILENAME"]);

/** 
 * get input variables
 */
 
$width = $_GET['width'];
$image_file = $_GET['image'];
if (array_key_exists('height', $_GET) && is_numeric($_GET['height'])) $height = $_GET['height'];
else $height = '';

if (array_key_exists('method', $_GET) && $_GET['method']) $method = $_GET['method']; // crop, extend
if (array_key_exists('gravity', $_GET) && $_GET['gravity']) $gravity = $_GET['gravity']; // northwest, north, northeast, west, center, east, southwest, south, southeast
if (array_key_exists('fill', $_GET) && is_numeric($_GET['fill'])) $fill = $_GET['fill']; // 0 or 1
		
/**
 * include configuration
 */
 
require_once("$dir/../conf/global.php");

/**
 * Set include paths
 */
 
set_include_path(ONXSHOP_PROJECT_DIR . PATH_SEPARATOR . ONXSHOP_DIR . PATH_SEPARATOR . ONXSHOP_DIR . 'lib/' . PATH_SEPARATOR . get_include_path());

require_once('lib/onxshop.functions.php');
require_once('model.php');

/**
 * onxshop_conf local overwrite due to missing database connection
 * See https://github.com/laposa/onxshop/issues/8
 */

$GLOBALS['onxshop_conf'] = array();
 
$local_configuration_overwrite_file = ONXSHOP_PROJECT_DIR . 'conf/common_image.php';
if (file_exists($local_configuration_overwrite_file)) include_once($local_configuration_overwrite_file);

/**
 * get common_image configuration
 */
 
require_once(ONXSHOP_DIR . "models/common/common_image.php");
$image_configuration = common_image::initConfiguration();
	
/**
 * check requested width
 */

if ($width > $image_configuration['width_max']) {

	$image_file = null;
	
} else {

	if ($width < $image_configuration['thumbnail_width_min']) $image_file = null;
	if ($width > $image_configuration['thumbnail_width_max']) $image_file = null;
	if ($width%$image_configuration['thumbnail_step'] > 0) $image_file = null;
	if (!is_readable(ONXSHOP_PROJECT_DIR . $image_file)) $image_file = null;

	if ($image_file) {

		/**
		 * get content type
		 */
		 
		$mime_type = local_exec("file -bi " . escapeshellarg(ONXSHOP_PROJECT_DIR . $image_file));
		$mime_type = trim($mime_type);
		
		/**
		 * check what to display
		 */
		 
		if (preg_match("/image/", $mime_type)) {
			//if image, process it
		} else if ($mime_type == 'application/pdf') {
			$image_file = "public_html/share/images/mimetype/pdf.png";	
		} else if ($mime_type == 'application/msword') {
			$image_file = "public_html/share/images/mimetype/document.png";	
		} else if ($mime_type == 'application/vnd.ms-excel') {
			$image_file = "public_html/share/images/mimetype/spreadsheet.png";
		} else {
			$image_file = "public_html/share/images/mimetype/ascii.png";	
		}
		
		/**
		 * try
		 */
		
		if ($thumbnail = common_image::resize($image_file, $width, $height, $method, $gravity, $fill)) $image_file = $thumbnail;
		
	} else {

		$image_file = null;
	}
}


/**
 * send image to the client through our image get script
 */
 
$_GET['image'] = $image_file;
include("image_get.php");

