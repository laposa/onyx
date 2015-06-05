<?php
/** 
 * 
 * Copyright (c) 2006-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/menu_js.php');

class Onxshop_Controller_Bo_Component_Node_Parent_Menu extends Onxshop_Controller_Component_Menu_Js {
	
	/**
	 * get list
	 */
	 
	public function getList($publish = 1) {
	
		require_once('models/common/common_node.php');
		
		
		/**
		 * what node elements to display?
		 */
		 
		if ($this->GET['parent_type'] !== '') {
			switch ($this->GET['parent_type']) {
				case 'layout':
					$node_group = 'page';
					break;
				case 'content':
					$node_group = 'layout';
					break;
				case 'page':
				default:
					$node_group = 'page';
					break;
			}
		} else {
			$node_group = 'page';
		}
		
		$Node = new common_node();
		$list = $Node->getTree(0, $node_group);		

		return $list;
	}
}
