<?php
/** 
 * Copyright (c) 2006-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Backoffice_Wrapper extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		header('X-Frame-Options: SAMEORIGIN');

		return true;
	}
}
