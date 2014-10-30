<?php
/** 
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'controllers/bo/backoffice_menu.php';

class Onxshop_Controller_Bo_Backoffice_Menu_Advanced extends Onxshop_Controller_Bo_Backoffice_Menu {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * Manage Advanced Menu
		 */
		if (preg_match('/backoffice/', $_SERVER['REQUEST_URI'])) {
			$active_array = explode("/", $_SERVER['REQUEST_URI']);
			$active = preg_replace("/\?.*$/", "", $active_array[3]);
		} else {
			$active = 'configuration';
		}

		$this->tpl->assign("ACTIVE_{$active}", 'active');

		/**
		 * ACL
		 */

		$auth = Onxshop_Bo_Authentication::getInstance();
		$isEcommerce = $auth->isEcommerce();

		if ($auth->hasPermission(ONXSHOP_PERMISSION_MEDIA_SECTION)) $this->tpl->parse('content.media');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_TAXONOMY_SECTION)) $this->tpl->parse('content.taxonomy');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_SEO_MANAGER_SECTION)) $this->tpl->parse('content.seo_manager');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_DATABASE_SECTION)) $this->tpl->parse('content.database');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_TEMPLATES_SECTION)) $this->tpl->parse('content.templates');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_SCHEDULER_SECTION)) $this->tpl->parse('content.scheduler');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_CURRENCY_SECTION)) $this->tpl->parse('content.currency');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_SEARCH_INDEX_SECTION)) $this->tpl->parse('content.search_index');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_TOOLS_SECTION)) $this->tpl->parse('content.tools');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_LOGS_SECTION)) $this->tpl->parse('content.logs');
		if ($auth->hasPermission(ONXSHOP_PERMISSION_CONFIGURATION_SECTION)) $this->tpl->parse('content.configuration');

		return true;
		
	}
}
