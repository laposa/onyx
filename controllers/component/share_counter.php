<?php
/** 
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Share_Counter extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * get input
		 */
		 
		$node_id = $this->GET['node_id'];
		
		/**
		 * check value
		 */
		 
		if (!is_numeric($node_id)) return false;
		
		/**
		 * initialize
		 */
		 
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		/**
		 * increment value
		 */
		 
		$Node->incrementShareCounter($node_id);

		return true;
		
	}
}
