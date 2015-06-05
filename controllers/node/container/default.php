<?php
/**
 * Copyright (c) 2009-2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/node/page/default.php');

class Onxshop_Controller_Node_Container_Default extends Onxshop_Controller_Node {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * include node configuration
		 */
		
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		/**
		 * nothing to do here, forward first parent page
		 */
		
		$first_parent_page_id = $Node->getParentPageId($this->GET['id']);
				
		onxshopGoTo("page/" . $first_parent_page_id);
		
		return true;
	}
}
