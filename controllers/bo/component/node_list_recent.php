<?php
/**
 * Copyright (c) 2014-2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Node_List_Recent extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/common/common_node.php');
		
		$Node = new common_node();
		
		$node_list = $Node->listing('', 'modified DESC', '0,20');
				
		foreach ($node_list as $item) {
			
			$item['latest_change_by'] = $Node->getCustomerIdForLastModified($item['id']);
			
			if ($item['publish'] == 0)  $item['class'] = 'disabled';
			$this->tpl->assign("ITEM", $item);
			$this->tpl->parse('content.item');
			
		}
		
		return true;
	}
		
}
