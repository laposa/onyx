<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_node.php');

class Onxshop_Controller_Component_Store_Nearby extends Onxshop_Controller {

	/**
	 * main action
	 */
	public function mainAction()
	{
		// get selected store for detail
		$node_id = (int) $this->GET['node_id'];

		if ($node_id > 0) {

			$Node = new common_node();
			$node = $Node->detail($node_id);

			$siblings = $Node->listing("node_group = 'page' AND node_controller = 'store' AND content ~ '[0-9]+' AND parent = {$node['parent']}");

			if (count($siblings) > 0) {

				foreach ($siblings as $i => $sibling) {
					$column = $i % 3 + 1;
					$this->tpl->assign("STORE", $sibling);
					$this->tpl->parse("content.list.column$column");
				}
				$this->tpl->parse("content.list");
			}
		}

		return true;
	}

}

