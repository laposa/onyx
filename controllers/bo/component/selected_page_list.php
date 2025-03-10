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
								$this->tpl->assign('PAGE_NAME', '<a onclick="openEdit(\'/popup/edit/content/' . str_replace(' ', '', $id) . '/orig/page/' . $node_data['parent'] . '\')">' . $node_detail['title'] . '</a>');
								$this->tpl->assign('PAGE_ID', $id);
								$this->tpl->assign('PAGE_VISIBILITY', $this->setPublished($node_detail['publish']));
								$this->tpl->parse('content.list.page');
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

    function setPublished($published) {
        return $published > 0 ? '<i style="color:green">published</i>' : '<b style="color:red">not published</b>';
    }
}
