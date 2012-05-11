<?php
/**
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/default.php');

class Onxshop_Controller_Node_Page_Default extends Onxshop_Controller_Node_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		//force to nonSSL
		/*
		if ($node_data['require_ssl'] == 0) {
			if ($_SERVER['SSL_PROTOCOL'] && $_SESSION['authentication']['authenticity'] < 1) {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
				exit;
			}
		}
		*/
		
		$this->processContainers();
		$this->processPage();

		return true;
	}
	
	/**
	 * process page
	 */
	 
	public function processPage() {
	
		if (!is_numeric($this->GET['id'])) return false;
		
		/**
		 * get node detail
		 */
		 
		require_once('models/common/common_node.php');
		$Node = new common_node();
		$node_data = $Node->nodeDetail($this->GET['id']);
		
		
		/**
		 * prepare variables
		 */
		 
		if ($node_data['page_title'] == '') $node_data['page_title'] = $node_data['title'];
		if (!isset($node_data['display_title'])) $node_data['display_title'] = $GLOBALS['onxshop_conf']['global']['display_title'];
		if (!isset($node_data['display_secondary_navigation'])) $node_data['display_secondary_navigation'] = $GLOBALS['onxshop_conf']['global']['display_secondary_navigation'];
		
		
		/**
		 * display page header
		 */
		 
		if ($node_data['display_title'])  {
			$_nSite = new nSite("component/page_header~id={$node_data['id']}~");
			$this->tpl->assign('PAGE_HEADER', $_nSite->getContent());
		}
		
		/**
		 * display secondary navigation
		 */
		 
		if ($node_data['display_secondary_navigation'] == 1) {
		
			$first_page_id = $Node->getFirstParentPage($_SESSION['active_pages']);
			//type=page_and_products
			$_nSite = new nSite("component/menu~level=0:expand_all=0:display_teaser=1:id={$first_page_id}:open={$node_data['id']}~");
			$this->tpl->assign('SECONDARY_NAVIGATION', $_nSite->getContent());
			$this->tpl->parse('content.secondary_navigation');
		}
		
		/**
		 * add related_taxonomy
		 */
		
		$node_data['related_taxonomy'] = $Node->getRelatedTaxonomy($node_data['id']);
		
		/**
		 * create taxonomy_class from related_taxonomy
		 */
		
		$node_data['taxonomy_class'] = '';
		
		foreach ($node_data['related_taxonomy'] as $t_item) {
			$node_data['taxonomy_class'] .= "t{$t_item['id']} ";
		}


		/**
		 * save node_controller and page css_class into registry to be used in sys/(x)html* as body class
		 */
		 
		Zend_Registry::set('body_css_class', "{$node_data['node_controller']} {$node_data['css_class']}");
		
		
		/**
		 * assign to template
		 */
		 
		$this->tpl->assign("NODE", $node_data);
		
		return true;
	}
	
	/**
	 * hook before parsing
	 */
	 
	public function parseContentTagsBeforeHook() {
		
		/**
		 * set active pages
		 */
		 
		$this->setActivePages();
		
		return true;
	}
	
	
	/**
	 * set active pages
	 */
		 
	public function setActivePages() {
		
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		$_SESSION['active_pages'] = $Node->getActivePages($this->GET['id']);
		$_SESSION['full_path'] = $Node->getFullPath($this->GET['id']);
	}
	
}
