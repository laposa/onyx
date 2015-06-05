<?php
/**
 * Pages controller
 *
 * Copyright (c) 2008-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Pages_Pages extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {

		$content_id = $this->getContentId();
		
		$this->tpl->assign('NODE_ID', $content_id);
		
		$_Onxshop_Request = new Onxshop_Request("bo/component/node_edit~id=$content_id~");
		$node_detail = $_Onxshop_Request->getContent();
		$this->tpl->assign("NODE_EDIT", $node_detail);

		return true;
	}
	
	/**
	 * get content_id
	 */
	 
	public function getContentId() {
		
		if (is_numeric($this->GET['id'])) {
			$content_id = $this->GET['id'];
		} else if (count($_SESSION['active_pages']) > 0) {
			$last_page_id = $this->Node->getLastParentPage($_SESSION['active_pages']);
			$content_id = $last_page_id;
		} else {
			$content_id = 0;
		}
		
		return $content_id;
		
	}
	/**
	 * hook before content tags parsed
	 */

	function parseContentTagsBeforeHook() {

		require_once('models/common/common_node.php');
		$this->Node = new common_node();
		
		$content_id = $this->getContentId();
		
        $_SESSION['active_pages'] = $this->Node->getActiveNodes($content_id, array('page', 'container'));
        $_SESSION['full_path'] = $this->Node->getFullPath($content_id);
        
		return true;

	}
}
