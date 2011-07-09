<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
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
		$node_conf = common_node::initConfiguration();
		
		/**
		 * nothing to do here, forward to homepage
		 */
		onxshopGoTo("page/" . $node_conf['id_map-homepage']);

		return true;
	}
}
