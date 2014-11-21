<?php
/** 
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'controllers/bo/backoffice_menu.php';

class Onxshop_Controller_Bo_Backoffice_Menu_Sections extends Onxshop_Controller_Bo_Backoffice_Menu {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * Manage Sections Menu
		 */
		
		if (preg_match('/backoffice/', $_SERVER['REQUEST_URI'])) {
			$active_array = explode("/", $_SERVER['REQUEST_URI']);
			$active = preg_replace("/\?.*$/", "", $active_array[2]);
		} else {
			$active = 'pages';
		}
		
		$this->tpl->assign("ACTIVE_{$active}", 'active');

		/**
		 * ACL
		 */

		$auth = Onxshop_Bo_Authentication::getInstance();
		$isEcommerce = $auth->isEcommerce();

		if ($auth->hasPermission('front_office')) $this->tpl->parse('content.fe_edit');
		if ($auth->hasPermission('nodes')) $this->tpl->parse('content.pages');
		if ($auth->hasPermission('nodes')) $this->tpl->parse('content.news');
		if ($auth->hasPermission('products') && $isEcommerce) $this->tpl->parse('content.products');
		if ($auth->hasPermission('recipes') && $isEcommerce) $this->tpl->parse('content.recipes');
		if ($auth->hasPermission('stores') && $isEcommerce) $this->tpl->parse('content.stores');
		if ($auth->hasPermission('orders') && $isEcommerce) $this->tpl->parse('content.orders');
		if ($auth->hasPermission('stock') && $isEcommerce) $this->tpl->parse('content.stock');
		if ($auth->hasPermission('customers')) $this->tpl->parse('content.customers');
		if ($auth->hasPermission('reports') && $isEcommerce) $this->tpl->parse('content.stats');
		if ($auth->hasPermission('discounts') && $isEcommerce) $this->tpl->parse('content.marketing');
		if ($auth->hasPermission('comments')) $this->tpl->parse('content.comments');
		if ($auth->hasPermission('surveys')) $this->tpl->parse('content.surveys');
		if ($auth->hasPermission('_all_')) $this->tpl->parse('content.advanced');

		return true;
		
	}
}
