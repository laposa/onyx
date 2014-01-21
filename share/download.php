<?php
/**
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
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
	//$file = ONXSHOP_PROJECT_DIR . "public_html/share/images/missing_image.png";
	header("HTTP/1.0 404 Not Found");
	echo "missing";
	// log it
} else {
	//admin user can download any content from var/ directory
	if ($_SESSION['authentication']['authenticity'] > 0) {
		$check = addcslashes(ONXSHOP_PROJECT_DIR, '/') . 'var\/';
	} else {
		//guest user can download only content of var/files
		//$check = addcslashes(ONXSHOP_PROJECT_DIR, '/') . 'var\/images\/';
		$check = addcslashes(ONXSHOP_PROJECT_DIR, '/') . 'var\/files\/';
	}

	if (!preg_match("/$check/", $realpath)) {
		header("HTTP/1.0 403 Forbidden");
		echo "forbidden";
		exit;
	}

	/**
	 * Detect file type and send to the clien
	 */
	 
	$mimetype = local_exec("file -bi " . escapeshellarg($file));
	header('Pragma: private');
	header('Cache-control: private, must-revalidate');
	header("Content-type: $mimetype");
	header('Content-Disposition: attachment; filename='.basename($file));
	header("Content-Length: " . filesize($file));
	ob_end_clean();
	$bytes = readfile($file);
	session_write_close();
	exit;
}

