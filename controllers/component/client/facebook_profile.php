<?php
/** 
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'controllers/component/client/facebook.php';

class Onxshop_Controller_Component_Client_Facebook_Profile extends Onxshop_Controller_Component_Client_Facebook {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * input
		 */
		
		if ($this->GET['fb_username']) $fb_username = $this->GET['fb_username'];
		else $fb_username = 'me';
		
		/**
		 * call shared actions
		 */
		 
		$this->commonAction();
		
		$user = $this->Facebook->getUser();
		
		if ($user) {
			// This call will always work since we are fetching public data.
			$user_profile = $this->Facebook->api("/$fb_username");
			
			if ($user_profile) {
				
				$this->tpl->assign('USER_PROFILE', $user_profile);
				$this->tpl->parse('content.profile');
			}
		}
		
		return true;
		
	}
	
}
