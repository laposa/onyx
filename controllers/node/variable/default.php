<?php
/**
 * Copyright (c) 2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/node/default.php');

class Onxshop_Controller_Node_Variable_Default extends Onxshop_Controller_Node_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		if ($this->processContent()) return true;
		else return false;
		
	}
	
	/**
	 * process content
	 */
	 
	public function processContent() {
		
		$node_id = $this->GET['id'];
		
		if (!is_numeric($node_id)) {
			msg('node/element/default: id not numeric', 'error');
			return false;
		}
		
		require_once('models/common/common_node.php');
		
		$this->Node = new common_node();
		
		$this->node_data = $this->Node->nodeDetail($node_id);
		
		$this->tpl->assign("NODE", $this->node_data);
		
		return true;
	}
	
}
