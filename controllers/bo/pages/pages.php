<?php
/**
 * Pages controller
 *
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Pages_Pages extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/common/common_node.php');
		
		$Node = new common_node();
		
		
		if (is_numeric($this->GET['id'])) {
			$content_id = $this->GET['id'];
		} else if (count($_SESSION['active_pages']) > 0) {
			$last_page_id = $Node->getFirstParentPage($_SESSION['active_pages']);
			$content_id = $last_page_id;
		} else {
			$content_id = 0;
		}
		
		$_Onxshop_Request = new Onxshop_Request("bo/component/node_edit~id=$content_id~");
		$node_detail = $_Onxshop_Request->getContent();
		$this->tpl->assign("NODE_EDIT", $node_detail);

		return true;
	}
}
