<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Sys_401 extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		header( "HTTP/1.0 401 Unauthorized");
		
		return true;
		// log it
	}
}
