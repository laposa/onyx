<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Backoffice_Wrapper extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		header('X-Frame-Options: SAMEORIGIN');
		
		/*don't need, it's secured in bootstrap
		require_once('lib/onxshop.authentication.php');
		$Auth = new Onxshop_Authentication();
		
		if ($_SESSION['authentication']['authenticity'] < 1 && $_GET['login'] != 1) {
			onxshopGoTo("/?login=1");
		} else if ($_SESSION['authentication']['authenticity'] < 1) {
			$Auth->login();
		}
		*/

		return true;
	}
}
