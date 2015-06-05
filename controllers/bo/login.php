<?php
/** 
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Login extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * show twitter and facebook only if app ID is configured
		 */
		
		/**
		if (ONXSHOP_FACEBOOK_APP_ID) $this->tpl->parse('content.choose_login_type.facebook');
		if (ONXSHOP_TWITTER_APP_ID) $this->tpl->parse('content.choose_login_type.twitter');
		
		if (ONXSHOP_FACEBOOK_APP_ID || ONXSHOP_TWITTER_APP_ID) $this->tpl->parse('content.choose_login_type');
		*/
		
		return true;
		
	}
}
