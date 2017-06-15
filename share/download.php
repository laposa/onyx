<?php
/**
 * Copyright (c) 2006-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

/**
 * rangeDownload
 * source: https://mobiforge.com/design-development/content-delivery-mobile-devices
 */
 
function rangeDownload($file) {
 
	$fp = @fopen($file, 'rb');
 
	$size   = filesize($file); // File size
	$length = $size;           // Content length
	$start  = 0;               // Start byte
	$end    = $size - 1;       // End byte
	// Now that we've gotten so far without errors we send the accept range header
	/* At the moment we only support single ranges.
	 * Multiple ranges requires some more work to ensure it works correctly
	 * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
	 *
	 * Multirange support annouces itself with:
	 * header('Accept-Ranges: bytes');
	 *
	 * Multirange content must be sent with multipart/byteranges mediatype,
	 * (mediatype = mimetype)
	 * as well as a boundry header to indicate the various chunks of data.
	 */
	header("Accept-Ranges: 0-$length");
	// header('Accept-Ranges: bytes');
	// multipart/byteranges
	// http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
	if (isset($_SERVER['HTTP_RANGE'])) {
	
		$c_start = $start;
		$c_end   = $end;
		// Extract the range string
		list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
		// Make sure the client hasn't sent us a multibyte range
		if (strpos($range, ',') !== false) {
		
			// (?) Shoud this be issued here, or should the first
			// range be used? Or should the header be ignored and
			// we output the whole content?
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			// (?) Echo some info to the client?
			exit;
		}
		// If the range starts with an '-' we start from the beginning
		// If not, we forward the file pointer
		// And make sure to get the end byte if spesified
		if ($range0 == '-') {
		
			// The n-number of the last bytes is requested
			$c_start = $size - substr($range, 1);
		}
		else {
		
			$range  = explode('-', $range);
			$c_start = $range[0];
			$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
		}
		/* Check the range and make sure it's treated according to the specs.
		 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
		 */
		// End bytes can not be larger than $end.
		$c_end = ($c_end > $end) ? $end : $c_end;
		// Validate the requested range and return an error if it's not correct.
		if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
		
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			// (?) Echo some info to the client?
			exit;
		}
		$start  = $c_start;
		$end    = $c_end;
		$length = $end - $start + 1; // Calculate new content length
		fseek($fp, $start);
		header('HTTP/1.1 206 Partial Content');
	}
	// Notify the client the byte range we'll be outputting
	header("Content-Range: bytes $start-$end/$size");
	header("Content-Length: $length");
 
	// Start buffered download
	$buffer = 1024 * 8;
	while(!feof($fp) && ($p = ftell($fp)) <= $end) {
	
		if ($p + $buffer > $end) {
		
			// In case we're only outputtin a chunk, make sure we don't
			// read past the length
			$buffer = $end - $p + 1;
		}
		set_time_limit(0); // Reset time limit for big files
		echo fread($fp, $buffer);
		flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
	}
 
	fclose($fp);
                  
}

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
	if (Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
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
	 * Detect file type and send to the client
	 */
	
	$mimetype = mime_content_type($file);
	header("Content-type: $mimetype");
	
	if (!isset($_GET['view'])) {
		header('Pragma: private');
		header('Cache-control: private, must-revalidate');
		header('Content-Disposition: attachment; filename='.basename($file));
	}
	
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

