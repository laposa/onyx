<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
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
		else $fb_username = 'onxshop';
		
		/**
		 * call shared actions
		 */
		 
		$this->commonAction();
		
		// This call will always work since we are fetching public data.
		$fb_user_public_detail = $this->Facebook->api("/$fb_username");
		
		$this->tpl->assign('FB_USER_PUBLIC_DETAIL', $fb_user_public_detail);
		
		return true;
		
	}
	
}
