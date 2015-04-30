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

		$active_page = 'pages';
		$active_subpage = '';
		
		if (preg_match('/backoffice/', $_SERVER['REQUEST_URI'])) {
			$active_array = explode("/", $_SERVER['REQUEST_URI']);
			$active_page = preg_replace("/\?.*$/", "", $active_array[2]);
			if (count($active_array) > 2) $active_subpage = preg_replace("/\?.*$/", "", $active_array[3]);
		}

		$this->tpl->assign("ACTIVE_{$active_page}", 'active');
		$this->tpl->assign("ACTIVE_{$active_page}_{$active_subpage}", 'active');

		/**
		 * ACL
		 */

		$auth = Onxshop_Bo_Authentication::getInstance();
		$isEcommerce = $auth->isEcommerce();

		if ($auth->hasAnyPermission('front_office')) $this->tpl->parse('content.fe_edit');
		if ($auth->hasAnyPermission('nodes')) $this->tpl->parse('content.pages');
		if ($auth->hasAnyPermission('nodes')) $this->tpl->parse('content.news');
		if ($auth->hasAnyPermission('products') && $isEcommerce) $this->tpl->parse('content.products');
		if ($auth->hasAnyPermission('recipes') && $isEcommerce) $this->tpl->parse('content.recipes');
		if ($auth->hasAnyPermission('stores') && $isEcommerce) $this->tpl->parse('content.stores');
		if ($auth->hasAnyPermission('orders') && $isEcommerce) $this->tpl->parse('content.orders');
		if ($auth->hasAnyPermission('stock') && $isEcommerce) $this->tpl->parse('content.stock');
		if ($auth->hasAnyPermission('customers')) $this->tpl->parse('content.customers');
		if ($auth->hasAnyPermission('reports') && $isEcommerce) $this->tpl->parse('content.stats');
		if ($auth->hasAnyPermission('discounts') && $isEcommerce) $this->tpl->parse('content.marketing');
		if ($auth->hasAnyPermission('comments')) $this->tpl->parse('content.comments');
		if ($auth->hasAnyPermission('surveys')) $this->tpl->parse('content.surveys');
		if ($auth->hasAnyPermission('_all_')) $this->tpl->parse('content.advanced');

		return true;
		
	}
}
