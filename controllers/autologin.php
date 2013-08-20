<?php
/**
 * Copyright (c) 2008-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


class Onxshop_Controller_Autologin extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		if ($_SESSION['client']['customer']['id'] == 0) {

			$this->checkCookieForToken();
			//$this->checkFacebookToken();

		}

		return true;

	}

	protected function checkFacebookToken()
	{
		if (isset($_COOKIE['fbsr_' . ONXSHOP_FACEBOOK_APP_ID])) {
		 	$request = new Onxshop_Request('component/client/facebook_auth');
			$_SESSION['use_page_cache'] = false;
		}

	}

	protected function checkCookieForToken()
	{
		if (isset($_COOKIE['onxshop_token'])) {

			require_once('models/client/client_customer_token.php');
			$Token = new client_customer_token();
			$Token->setCacheable(false);
		
			$customer_detail = $Token->getCustomerDetailForToken($_COOKIE['onxshop_token']);

			if ($customer_detail) {

				require_once('models/client/client_customer.php');
				$Customer = new client_customer();
				$Customer->setCacheable(false);
				$conf = $Customer::initConfiguration();

				if ($conf['login_type'] == 'username') $username = $customer_detail['username'];
				else $username = $customer_detail['email'];

				$customer_detail = $Customer->login($username, $customer_detail['password']);

				if ($customer_detail) {
					$_SESSION['client']['customer'] = $customer_detail;
					$_SESSION['use_page_cache'] = false;
				} else {
					msg('Autologin failed', 'error', 1);
				}

			} else {

				msg('Invalid autologin token supplied', 'error', 1);
				//TODO: remove token from cookies
			}

		}
	}

}
