<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Node_Container_Content extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		
		$node_id = $this->GET['node_id'];
		$container = $this->GET['container'];
		
		
		if (!is_numeric($node_id) || !is_numeric($container)) return false;
		
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		$content = $Node->parseChildren($node_id, $container);
		
		foreach ($content as $item) {
			
			$this->tpl->assign('ITEM', $item);
			$this->tpl->parse('content.item');
			
		}
		
		return true;
		
	}
}
