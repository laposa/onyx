<?php
/** 
 * Copyright (c) 2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');
require_once('models/common/common_node.php');

class Onxshop_Controller_Node_Content_Teaser_List extends Onxshop_Controller_Node_Content_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$this->Node = new common_node();

		$node_data = $this->Node->nodeDetail($this->GET['id']);

		/**
		 * call controller
		 */

		$node_ids = explode(",", trim($node_data['component']['node_ids']));
		$content = '';

		foreach ($node_ids as $node_id) {

			if (is_numeric($node_id)) {
				$_Onxshop_Request = new Onxshop_Request("component/teaser~target_node_id={$node_id}~");
				$content .= $_Onxshop_Request->getContent();
			}

		}

		$this->tpl->assign('CONTENT', $content);
		$this->tpl->assign('NODE', $node_data);

		if ($node_data['display_title'])  $this->tpl->parse('content.title');

		return true;
	}


}
