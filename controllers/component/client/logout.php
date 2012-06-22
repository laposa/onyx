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
			
				//$_SESSION['client']['customer']['id'] = 0;
				unset($_SESSION['client']);
			
				//clean cookies
				$this->cleanCookies();
				
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
}
