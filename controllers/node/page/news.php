<?php
/**
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/page/default.php');

class Onxshop_Controller_Node_Page_News extends Onxshop_Controller_Node_Page_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/common/common_node.php');
		$Node = new common_node();
		$node_data = $Node->nodeDetail($this->GET['id']);
		
		if ($node_data['component']['allow_comment'] == 1) {
			$_nSite = new nSite("component/comment~node_id={$this->GET['id']}:allow_anonymouse_submit=1~");
			$this->tpl->assign("COMMENT", $_nSite->getContent());
			$this->tpl->parse("content.comment");
		}
		
		$this->processContainers();
		$this->processPage();

		return true;
	}
}
