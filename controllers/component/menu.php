<?php
/** 
 * Copyright (c) 2005-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/tree.php');

class Onxshop_Controller_Component_Menu extends Onxshop_Controller_Tree {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * input variables
		 */
		
		// how deep we can go, zero means unlimited
		if (isset($this->GET['level'])) $max_display_level = $this->GET['level'];
		else $max_display_level = 0;
		
		// if to expand all items, when 1 show all (ie for sitemap), 0 expands only active items
		if ($this->GET['expand_all'] == 1) $expand_all = 1;
		else $expand_all = 0;
		
		// 1 parse strapline
		if ($this->GET['display_strapline'] == 1) $display_strapline = 1;
		else $display_strapline = 0;
		
		// 1 shows only published items, 0 shows all
		// possible security flaw, user can see list of not published items if provide the get parameter
		if (is_numeric($this->GET['publish'])) $publish = $this->GET['publish'];
		else $publish = 1;
		
		// open this item (active item)
		if (is_numeric($this->GET['open'])) $open = $this->GET['open'];
		else $open = null;
		
		// node_id
		if (is_numeric($this->GET['id'])) $node_id = $this->GET['id'];
		else $node_id = null; //null if not provided (it's correct value for tree's root elements)
		
		// filter (see common_node->prepareNodeGroupFilter() for available filters)
		if (isset($this->GET['filter'])) {
			
			$filter = $this->GET['filter'];
			
		} else {
			
			if (ONXSHOP_ECOMMERCE === true) $filter = 'page_exclude_products_recipes'; // don't show products in navigation on ecommerce sites as could have large product database
			else $filter = 'page';
			
		}
		
		/**
		 * process action
		 */
		
		return $this->standardAction($node_id, $publish, $max_display_level, $expand_all, $filter, $node_controller);
		
	}

	/**
	 * Is given node active? I.e. is it or its parent selected/open?
	 * Override if necessary
	 */
	protected function isNodeActive(&$item)
	{
		if (is_numeric($this->GET['active_page'])) {
			if ($item['id'] == $this->GET['active_page']) return true;
		} else {
			return (in_array($item['id'], $_SESSION['active_pages']));
		}
	}

	/**
	 * Is given node open? Override if necessary
	 */
	protected function isNodeOpen(&$item)
	{
		if (is_numeric($this->GET['open']) && $item['id'] == $this->GET['open']) return true;
		return ($item['id'] == $_SESSION['active_pages'][0]);
	}
	
}
