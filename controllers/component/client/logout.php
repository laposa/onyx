<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Client_Logout extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		if ($_SESSION['client']['customer']['id'] > 0) {
			
			require_once('models/client/client_customer.php');
			$ClientCustomer = new client_customer();
			
			if ($ClientCustomer->logout()) {
			
				msg("Logout of {$_SESSION['client']['customer']['email']}", 'ok', 1);
				
				//$_SESSION['client']['customer']['id'] = 0;
				unset($_SESSION['client']);
			
				//clean cookies
				$this->cleanCookies();
				
				//clean facebook auth
				$this->logoutFromFacebook();
				
				//forward to the homepage
				onxshopGoTo(AFTER_CLIENT_LOGOUT_URL);
			
			} else {
			
				msg("Customer logout failed", 'error');
			
			}
		}

		return true;
	}
	
	/**
	 * cleanCookies
	 */
	 
	public function cleanCookies() {
	
		// delete session cookie.
		// this is not what we need, because it will cause logout from backoffice
		//if (ini_get("session.use_cookies")) {
		//    $params = session_get_cookie_params();
		//    setcookie(session_name(), '', time() - 42000,
		//        $params["path"], $params["domain"],
		//        $params["secure"], $params["httponly"]
		//    );
		//}
		
		/**
		 * Delete autologin cookies - the time must be in the past,
		 * so just negate what you added when creating the
		 * cookie.
		 */
		 
		if(isset($_COOKIE['autologin_username']) && isset($_COOKIE['autologin_md5_password'])){
			setcookie("autologin_username", "", time()-60*60*24*100, "/");
			setcookie("autologin_md5_password", "", time()-60*60*24*100, "/");
		}
		
	}
	
	
	/**
	 * logoutFromFacebook
	 */
	 
	public function logoutFromFacebook() {
	
		require_once 'lib/facebook/facebook.php';
		
		/**
		 * conf in deployment.php
		 */
		 
		$facebook_conf = array(
			'appId'  => ONXSHOP_FACEBOOK_APP_ID,
			'secret' => ONXSHOP_FACEBOOK_APP_SECRET
		);
		
		$this->Facebook = new Facebook($facebook_conf);
		
		$this->Facebook->destroySession();
	
	}
}
