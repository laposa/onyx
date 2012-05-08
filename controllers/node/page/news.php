<?php
/**
 * Copyright (c) 2006-2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/page/default.php');

class Onxshop_Controller_Node_Page_News extends Onxshop_Controller_Node_Page_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		//input data
		if (is_numeric($this->GET['id'])) $node_id = $this->GET['id'];
		else return false;
		
		//initialise
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		//get node detail
		$node_data = $Node->nodeDetail($node_id);
		
		//parent page is blog article container
		$blog_node_id = $node_data['parent'];
		$this->tpl->assign('BLOG_NODE_ID', $blog_node_id);
		
		//show comments only when enabled
		if ($node_data['component']['allow_comment'] == 1) {
			$_nSite = new nSite("component/comment~node_id={$node_id}:allow_anonymouse_submit=1~");
			$this->tpl->assign("COMMENT", $_nSite->getContent());
			$this->tpl->parse("content.comment");
		}
		
		//standard page actions
		$this->processContainers();
		$this->processPage();

		return true;
	}
}
