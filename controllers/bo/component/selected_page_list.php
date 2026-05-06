<?php
/**
 * Copyright (c) 2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onyx_Controller_Bo_Component_Selected_Page_List extends Onyx_Controller {
    public function mainAction() {

			require_once('models/common/common_node.php');
			$Node = new common_node();
			$node_data = $Node->nodeDetail($this->GET['node_id']);

			if($node_data ?? false) {
				$ids = explode(',', $node_data['component']['node_ids']);
				$not_empty = false;
				
				foreach ($ids as $id) {
						if(! empty($id)) {
								$node_detail = $Node->nodeDetail($id);
								$this->tpl->assign('ITEM', $node_detail);
								$this->tpl->parse('content.list.item');
								$not_empty = true;
						}
				}
	
				if($not_empty) {
						$this->tpl->parse('content.list');
				}

				$this->tpl->assign('ID_LIST', $node_data['component']['node_ids']);

				return true;
			
			} else {

				return false;

			}
    }
}
