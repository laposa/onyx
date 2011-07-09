<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/node_menu.php');

class Onxshop_Controller_Bo_Component_Node_Menu_Tinymce extends Onxshop_Controller_Bo_Component_Node_Menu {
	
	/**
	 * get list
	 */
	 
	public function getList($publish = 1) {
		
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		$list = $Node->getTree($publish, "page_and_product");
		
		return $list;
		
	}
}
