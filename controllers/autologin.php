<?php
/**
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


class Onxshop_Controller_Autologin extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/* Check if user has been remembered (only if is not already logged in)*/
		if ($_SESSION['client']['customer']['id'] == 0) {
			if(isset($_COOKIE['autologin_username']) && isset($_COOKIE['autologin_md5_password'])) {
				require_once('models/client/client_customer.php');
				$Customer = new client_customer();
			
				$customer_detail = $Customer->login($_COOKIE['autologin_username'], $_COOKIE['autologin_md5_password']);
				if ($customer_detail) {
					$_SESSION['client']['customer'] = $customer_detail;
					//disable ZendCache
					$_SESSION['use_page_cache'] = false;
				} else {
					msg('Autologin failed', 'error', 1);
				}
			}
		}

		return true;
	}
}
