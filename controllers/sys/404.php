<?php
/** 
 * Copyright (c) 2006-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Sys_404 extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * don't show error message for selected URIs to keep the error log clean
		 */
		 
		if (!in_array($_SERVER['REQUEST_URI'], $this->getSuppressedLogUriList())) {
		
			if ($_SERVER['HTTP_REFERER']) msg("Missing {$_SERVER['REQUEST_URI']} (linked from {$_SERVER['HTTP_REFERER']})", 'error');
			else msg("Missing {$_SERVER['REQUEST_URI']}", 'error');
		
		}
		
		/**
		 * set 404 header
		 */
		 
		$this->http_status = '404'; // is this still needed?
		header("HTTP/1.0 404 Not Found");
		
		return true;		

	}
	
	/**
	 * getSuppressedLogUriList
	 */
	
	public function getSuppressedLogUriList() {
		
		return array('/apple-touch-icon.png', '/apple-touch-icon-precomposed.png');
		
	}
}
