<?php
/** 
 * Copyright (c) 2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once("controllers/node.php");

class Onxshop_Controller_Node_Force_Config extends Onxshop_Controller_Node {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		$node_id = $this->GET['node_id'];

		if (is_numeric($node_id)) {
			$global_conf_node_overwrites = $this->initGlobalNodeConfigurationOverwrites($node_id);
			$GLOBALS['onxshop_conf'] = $this->array_replace_recursive($GLOBALS['onxshop_conf'], $global_conf_node_overwrites);
		}

		return true;
	}
	
}
