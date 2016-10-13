<?php
/** 
 * Copyright (c) 2006-2016 Onxshop Ltd (https://onxshop.com)
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
		 * set 404 HTTP code
		 */
		
		http_response_code(404);
		
		/**
		 * don't allow to save this request to the cache
		 */
		 
		Zend_Registry::set('omit_cache', true);
		
		return true;		

	}
	
	/**
	 * getSuppressedLogUriList
	 */
	
	public function getSuppressedLogUriList() {
		
		return array('/apple-touch-icon.png', '/apple-touch-icon-precomposed.png');
		
	}
}
