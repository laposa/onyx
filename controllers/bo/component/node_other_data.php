<?php
/** 
 * Copyright (c) 2012-2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Node_Other_Data extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * initialize
		 */
		 
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		/**
		 * get detail
		 */
		 
		$node_detail = $Node->getDetail($this->GET['id']);
		
		/**
		 * other data (attributes) list
		 */
		
		if (is_array($node_detail['other_data'])) {
			
			foreach ($node_detail['other_data'] as $key=>$value) {
				
				$note['key'] = $key;
				$note['value'] = $value;
				
				$this->tpl->assign('OTHER_DATA', $note);
				
				$allow_to_add_more = true; // we curently support editing only for scalar values
				
				if (is_array($value)) {
					
					$allow_to_add_more = false; // don't allow editing
					$this->tpl->parse('content.other_data.item_noneditable');
					
				} else {
					
					if ($note['key'] != '') {
						
						$this->tpl->parse('content.other_data.item_editable');
					}
				}
				
			}
			
			if ($allow_to_add_more) $this->tpl->parse('content.add');
			if (count($node_detail['other_data']) > 0) $this->tpl->parse('content.other_data');
		}

		return true;
		
	}
}
