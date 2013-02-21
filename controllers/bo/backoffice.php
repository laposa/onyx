<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
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
		
		$this->tpl->assign('USERNAME', ucfirst($_SESSION['authentication']['username']));
			
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
		 
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER) $this->tpl->parse('content.search');
		
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER || $_SESSION['authentication']['username'] == ONXSHOP_DB_USER . '-editor') $this->tpl->parse('content.fe_edit');
		
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER || $_SESSION['authentication']['username'] == ONXSHOP_DB_USER . '-editor') $this->tpl->parse('content.pages');
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER || $_SESSION['authentication']['username'] == ONXSHOP_DB_USER . '-editor') $this->tpl->parse('content.news');
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER || $_SESSION['authentication']['username'] == ONXSHOP_DB_USER . '-editor') $this->tpl->parse('content.media');
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER  && $this->isEcommerce()) $this->tpl->parse('content.products');
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER || preg_match("/-warehouse$/", $_SESSION['authentication']['username'])) {
			if ($this->isEcommerce()) $this->tpl->parse('content.orders');
		}
		if (preg_match("/-warehouse$/", $_SESSION['authentication']['username'])) $this->tpl->parse('content.stock');
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER) $this->tpl->parse('content.customers');
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER  && $this->isEcommerce()) $this->tpl->parse('content.stats');
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER && $this->isEcommerce()) $this->tpl->parse('content.marketing');
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER) $this->tpl->parse('content.comments');
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER) $this->tpl->parse('content.surveys');
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER) $this->tpl->parse('content.advanced');
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER) $this->tpl->parse('content.recipes');
		if ($_SESSION['authentication']['username'] == ONXSHOP_DB_USER) $this->tpl->parse('content.stores');

		return true;
	}
	
	/**
	 * isEcommerce
	 */
	 
	public function isEcommerce() {
		
		if (ONXSHOP_PACKAGE_NAME == 'standard' || ONXSHOP_PACKAGE_NAME == 'premium') return true;
		else return false; 
	
	}
}
