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
		 * ACL
		 */

		$auth = Onxshop_Bo_Authentication::getInstance();
		$isEcommerce = $auth->isEcommerce();

		if ($auth->hasPermission(ONXSHOP_PERMISSION_FRONT_END_EDITING)) $this->tpl->parse('content.fe_edit');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_PAGES_SECTION)) $this->tpl->parse('content.pages');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_NEWS_SECTION)) $this->tpl->parse('content.news');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_PRODUCTS_SECTION) && $isEcommerce) $this->tpl->parse('content.products');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_RECIPES_SECTION) && $isEcommerce) $this->tpl->parse('content.recipes');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_STORES_SECTION) && $isEcommerce) $this->tpl->parse('content.stores');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_ORDERS_SECTION) && $isEcommerce) $this->tpl->parse('content.orders');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_STOCK_SECTION) && $isEcommerce) $this->tpl->parse('content.stock');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_CUSTOMERS_SECTION)) $this->tpl->parse('content.customers');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_STATS_SECTION) && $isEcommerce) $this->tpl->parse('content.stats');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_MARKETING_SECTION) && $isEcommerce) $this->tpl->parse('content.marketing');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_COMMENTS_SECTION)) $this->tpl->parse('content.comments');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_SURVEYS_SECTION)) $this->tpl->parse('content.surveys');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_ADVANCED_SECTION)) $this->tpl->parse('content.advanced');

		return true;
	}
}
