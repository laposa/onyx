<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once 'lib/facebook/facebook.php';


class Onxshop_Controller_Component_Client_Facebook extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * call shared actions
		 */
		 
		$this->commonAction();

		
		return true;
		
	}
	
	/**
	 * common action
	 */
	 
	public function commonAction() {
		
		/**
		 * first catch errors
		 */
		 
		$this->catchFacebookErrorsViaGET();
		
		/**
		 * initiate facebook object
		 */
		 
		$this->initFacebook();
				
		return true;
		
	}
	
	/**
	 * initFacebook
	 */
	 
	public function initFacebook() {
		
		/**
		 * conf in deployment.php
		 */
		 
		$facebook_conf = array(
			'appId'  => ONXSHOP_FACEBOOK_APP_ID,
			'secret' => ONXSHOP_FACEBOOK_APP_SECRET
		);
		
		$this->Facebook = new Facebook($facebook_conf);
		
	}
	
	/**
	 * catchFacebookErrorsViaGET
	 * as they come via GET callback
	 */
	 
	public function catchFacebookErrorsViaGET() {
		
		if (array_key_exists('error_reason', $this->GET)) msg("error_reason: {$this->GET['error_reason']}", 'error', 1);
		if (array_key_exists('error', $this->GET)) msg("error: {$this->GET['error']}", 'error', 1);
		if (array_key_exists('error_description', $this->GET)) msg("error_description: {$this->GET['error_description']}", 'error', 1);
		if (array_key_exists('state', $this->GET)) msg("state: {$this->GET['state']}", 'error', 1);
		
	}
	
}