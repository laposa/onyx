<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'controllers/component/client/twitter.php';

class Onxshop_Controller_Component_Client_Twitter_Profile extends Onxshop_Controller_Component_Client_Twitter {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * call shared actions
		 */
		 
		$token = $this->commonAction();
		
		/**
		 * get user profile
		 */
		
		// verifyCredentials() tests if supplied user credentials are valid with minimal overhead.
		if ($token && $this->twitter->isAuthorised()) {
		
			$user_profile = $this->twitterCall('accountVerifyCredentials');
			
			if ($user_profile) {
				
				$this->tpl->assign('USER_PROFILE', $user_profile);
				$this->tpl->parse('content.profile');
			}
			
		}
		
		return true;
		
	}
	
}
