<?php
/** 
 * Copyright (c) 2006-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Sys_401 extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * set 401 HTTP code
		 */
		
		http_response_code(401);
		
		/**
		 * don't allow to save this request to the cache
		 */
		 
		Zend_Registry::set('omit_cache', true);
		
		return true;

	}
}
