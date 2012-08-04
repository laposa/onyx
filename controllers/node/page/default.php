<?php
/**
 * Copyright (c) 2008-2012 Laposa Ltd (http://laposa.co.uk)
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
			if (array_key_exists('HTTPS', $_SERVER) && $_SESSION['authentication']['authenticity'] < 1) {
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
		 * get related_taxonomy
		 */
		
		$related_taxonomy = $this->getNodeRelatedTaxonomy($node_data);
		
		/**
		 * create taxonomy class
		 */
		 
		$node_data['taxonomy_class'] = $this->createTaxonomyClass($related_taxonomy);
		
		/**
		 * save node_controller, page css_class and taxonomy_class into registry to be used in sys/(x)html* as body class
		 */
		
		$body_css_class = "{$node_data['node_controller']} {$node_data['css_class']} {$node_data['taxonomy_class']}";
		
		$this->saveBodyCssClass($body_css_class);
		
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
	
	/**
	 * get taxonomy related to node
	 */
	 
	public function getNodeRelatedTaxonomy($node_data) {
		
		if (!is_array($node_data)) return false;
		
		require_once('models/common/common_node.php');
		$Node = new common_node();
		$related_taxonomy = $Node->getRelatedTaxonomy($node_data['id']);
		
		return $related_taxonomy;
	}
	
	/**
	 * createTaxonomyClass from $related_taxonomy array
	 */
	 
	public function createTaxonomyClass($related_taxonomy) {
	
		if (!is_array($related_taxonomy)) return false;
		
		/**
		 * create taxonomy_class from related_taxonomy
		 */
		
		$taxonomy_class = '';
		
		foreach ($related_taxonomy as $t_item) {
			if (is_numeric($t_item['id'])) $taxonomy_class .= "t{$t_item['id']} ";
		}
		
		return $taxonomy_class;
		
	}
	
	/**
	 * saveBodyCssClass
	 */
	 
	public function saveBodyCssClass($body_css_class) {
		
		if (Zend_Registry::isRegistered('body_css_class')) {
		
			Zend_Registry::set('body_css_class', $body_css_class . ' ' . Zend_Registry::get('body_css_class'));
		
		} else {
		
			Zend_Registry::set('body_css_class', $body_css_class);
		
		}
		
	}
	
}
