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
			$_SESSION['client']['customer']['id'] = 0;
			unset($_SESSION['client']);
			/**
			 * Delete cookies - the time must be in the past,
			 * so just negate what you added when creating the
			 * cookie.
			 */
			if(isset($_COOKIE['autologin_username']) && isset($_COOKIE['autologin_md5_password'])){
				setcookie("autologin_username", "", time()-60*60*24*100, "/");
				setcookie("autologin_md5_password", "", time()-60*60*24*100, "/");
			}
		
			//forward to the homepage
			onxshopGoTo("/");
		}

		return true;
	}
}
