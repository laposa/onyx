<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'controllers/component/client/facebook.php';

class Onxshop_Controller_Component_Client_Facebook_Auth extends Onxshop_Controller_Component_Client_Facebook {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * call shared actions
		 */
		 
		$this->commonAction();
		
		/**
		 * login action handler (auth button sent)
		 */
		
		if ($_POST['facebook_auth']) {
	
			$login_conf = $this->getLoginConf();
			$fb_login_url = $this->Facebook->getLoginUrl($login_conf);
			OnxshopGoto($fb_login_url, 2);
			
		}
		
		// Get User ID
		$user = $this->Facebook->getUser(); //user is object, but it's string value is numeric
		
		// We may or may not have this data based on whether the user is logged in.
		//
		// If we have a $user id here, it means we know the user is logged into
		// Facebook, but we don't know if the access token is valid. An access
		// token is invalid if the user logged out of Facebook.
		
		if ($user) {
		
			try {
			
				// Proceed knowing you have a logged in user who's authenticated
				$user_profile = $this->Facebook->api('/me');
				$this->tpl->assign('USER_PROFILE', $user_profile);
				
				if ($user_profile['id']) {
					//try to login
					$this->loginToOnxshop($user_profile);
				}
				
			} catch (FacebookApiException $e) {
				
				msg($e, 'error', 1);
				$user = null;
			}
		}
		
		/**
		 * show login box based on status
		 */
		 		
		if ($user) {
		
			$this->tpl->parse('content.authorised');
		
		} else {
		
			$this->tpl->parse('content.login');
		
		}
		
		return true;
		
	}
	
	/**
	 * getLoginConf
	 */

	public function getLoginConf() {
		
		require_once('models/client/client_customer.php');
		$client_customer_conf = Client_Customer::initConfiguration();
		
		$conf = array('scope' => $client_customer_conf['facebook_login_scope']);
		
		return $conf;
	}
	
	/**
	 * loginToOnxshop
	 */
	 
	public function loginToOnxshop($user_profile) {
		
		require_once('models/client/client_customer.php');
		$Customer = new client_customer();
		$Customer->setCacheable(false);
		
		if ($customer_detail = $Customer->getUserByFacebookId($user_profile['id'])) {
		
			// already exists a valid account, we can login
			msg("{$customer_detail['email']} is already registered", 'ok', 1);
			$_SESSION['client']['customer'] = $customer_detail;	
			$_SESSION['use_page_cache'] = false;
			
			// auto login (TODO allow to enable/disable this behaviour)
			$Customer->generateAndSaveOnxshopToken($customer_detail['id']);
						
		} else {
		
			msg("{$user_profile['email']} (FB ID {$user_profile['id']}) successfully authorised over Facebook, but must register locally", 'ok', 1);
			
			//forward to registration
			$this->mapUserToOnxshop($user_profile);
			onxshopGoTo("/page/13");//TODO get node_id from common_node.conf
		
		}
	}
	
	/**
	 * mapUserToOnxshop
	 */
	 
	public function mapUserToOnxshop($user_profile) {
		
		//map to Onxshop schema
		$onxshop_client_customer = array();
		$onxshop_client_customer['first_name'] = $user_profile['first_name'];
		$onxshop_client_customer['last_name'] = $user_profile['last_name'];
		$onxshop_client_customer['email'] = $user_profile['email'];
		$onxshop_client_customer['facebook_id'] = $user_profile['id'];
		$onxshop_client_customer['profile_image_url'] = "https://graph.facebook.com/{$user_profile['id']}/picture";
		
		//save to session
		$_SESSION['r_client']['customer'] = $onxshop_client_customer;
		
	}
	
}
