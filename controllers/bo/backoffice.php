<?php
/** 
 * Copyright (c) 2006-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Backoffice extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		//force SSL
		if (!$_SERVER['HTTPS'] && ONXSHOP_EDITOR_USE_SSL) {
			header("Location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
		}
			
		/**
		 * Manage Menu
		 */
		
		if (preg_match('/backoffice/', $_SERVER['REQUEST_URI'])) {
			$active_array = explode("/", $_SERVER['REQUEST_URI']);
			$active = $active = preg_replace("/\?.*$/", "", $active_array[2]);
		} else {
			$active = 'pages';
		}
		
		$this->tpl->assign("ACTIVE_{$active}", 'active');
		
		/**
		 * ACL - show everything to all until client_acl is ready (TODO)
		 */

		if (Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {

			$this->tpl->parse('content.fe_edit');
			$this->tpl->parse('content.pages');
			$this->tpl->parse('content.news');
			$this->tpl->parse('content.media');
			$this->tpl->parse('content.products');
			$this->tpl->parse('content.orders');
			$this->tpl->parse('content.stock');
			$this->tpl->parse('content.customers');
			$this->tpl->parse('content.stats');
			$this->tpl->parse('content.marketing');
			$this->tpl->parse('content.comments');
			$this->tpl->parse('content.surveys');
			$this->tpl->parse('content.advanced');
			$this->tpl->parse('content.recipes');
			$this->tpl->parse('content.stores');

		}

		return true;
	}
}
