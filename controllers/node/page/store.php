<?php
/**
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('models/common/common_node.php');
require_once('controllers/node/page/default.php');

class Onxshop_Controller_Node_Page_Store extends Onxshop_Controller_Node_Page_Default {

	/**
	 * main action
	 */
	public function mainAction()
	{
		parent::mainAction();

		$node_id = (int) $this->GET['id'];

		// display Dublin district menu when in Dublin county
		if (is_numeric($node_id) && $node_id > 0) {
			$Node = new common_node();
			$node = $Node->detail($node_id);

			if ($node_id == 1536 || $node['parent'] == 1536) {
				$this->tpl->parse("content.dublin_menu");
			}
		}

		return true;
	}

}