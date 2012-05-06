<?php
/** 
 * Copyright (c) 2012 Laposa Ltd (http://laposa.co.uk)
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
				if ($note['key'] != '') {
					$this->tpl->assign('OTHER_DATA', $note);
					$this->tpl->parse('content.other_data.item');
				}
			}
			if (count($node_detail['other_data']) > 0) $this->tpl->parse('content.other_data');
		}

		return true;
		
	}
}
