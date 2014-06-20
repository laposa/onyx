<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
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
		
		// 1 parse teaser
		if ($this->GET['display_teaser'] == 1) $display_teaser = 1;
		else $display_teaser = 0;
		
		// 1 shows only published items, 0 shows all
		// possible security flaw, user can see list of not published items if provide the get parameter
		if (is_numeric($this->GET['publish'])) $publish = $this->GET['publish'];
		else $publish = 1;
		
		//open this item (active item)
		if (is_numeric($this->GET['open'])) $open = $this->GET['open'];
		else $open = null;
		
		//node_id
		if (is_numeric($this->GET['id'])) $node_id = $this->GET['id'];
		else $node_id = null; //null if not provided (it's correct value for tree's root elements)
		
		//node_group
		switch ($this->GET['node_group']) {
			case 'content':
				$node_group = 'all';
				break;
			case 'layout':
				$node_group = 'layout';
				break;
			case 'page_and_product':
				$node_group = 'page_and_product';
				break;
			case 'page':
			default:
				$node_group = 'page';
				break;
		}

		/**
		 * process action
		 */
		
		return $this->standardAction($node_id, $publish, $max_display_level, $expand_all, $node_group);
		
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
