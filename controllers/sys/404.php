<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Sys_404 extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		if ($_SERVER['HTTP_REFERER']) msg("Missing {$_SERVER['REQUEST_URI']} linked from {$_SERVER['HTTP_REFERER']}.", 'error', 2);
		else msg("Missing {$_SERVER['REQUEST_URI']}.", 'error', 2);
		
		$this->http_status = '404';
		header("HTTP/1.0 404 Not Found");
		
		return true;		
		// log it
	}
}
