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
		 * simple ACL
		 */
		 
		$username = $_SESSION['authentication']['username'];
		
		if ($GLOBALS['Auth']->isAdmin($username) || $GLOBALS['Auth']->isEditor($username)) $this->tpl->parse('content.fe_edit');
		
		if ($GLOBALS['Auth']->isAdmin($username) || $GLOBALS['Auth']->isEditor($username)) $this->tpl->parse('content.pages');
		if ($GLOBALS['Auth']->isAdmin($username) || $GLOBALS['Auth']->isEditor($username)) $this->tpl->parse('content.news');
		if ($GLOBALS['Auth']->isAdmin($username) || $GLOBALS['Auth']->isEditor($username)) $this->tpl->parse('content.media');
		if ($GLOBALS['Auth']->isAdmin($username)  && $GLOBALS['Auth']->isEcommerce()) $this->tpl->parse('content.products');
		if ($GLOBALS['Auth']->isAdmin($username) || $GLOBALS['Auth']->isWarehouse($username)) {
			if ($GLOBALS['Auth']->isEcommerce()) $this->tpl->parse('content.orders');
		}
		if ($GLOBALS['Auth']->isWarehouse($username)) $this->tpl->parse('content.stock');
		if ($GLOBALS['Auth']->isAdmin($username)) $this->tpl->parse('content.customers');
		if ($GLOBALS['Auth']->isAdmin($username) && $GLOBALS['Auth']->isEcommerce()) $this->tpl->parse('content.stats');
		if ($GLOBALS['Auth']->isAdmin($username) && $GLOBALS['Auth']->isEcommerce()) $this->tpl->parse('content.marketing');
		if ($GLOBALS['Auth']->isAdmin($username)) $this->tpl->parse('content.comments');
		if ($GLOBALS['Auth']->isAdmin($username)) $this->tpl->parse('content.surveys');
		if ($GLOBALS['Auth']->isAdmin($username)) $this->tpl->parse('content.advanced');
		if ($GLOBALS['Auth']->isAdmin($username) && $GLOBALS['Auth']->isEcommerce()) $this->tpl->parse('content.recipes');
		if ($GLOBALS['Auth']->isAdmin($username) && $GLOBALS['Auth']->isEcommerce()) $this->tpl->parse('content.stores');

		return true;
	}
}
