<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/x.php');
require_once('models/common/common_node.php');
class Onyx_Controller_Bo_Component_X_Node_Position extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */

    private $node_data;
     
    public function mainAction() {

        // get details
        $node = new common_node();
        $node_data = $node->nodeDetail($this->GET['node_id'] ?? $_POST['node']['id']);
        $parent_data = $node->nodeDetail($node_data['parent']);

        // save
        if (isset($_POST['save'])) {
            if($node->nodeUpdate($_POST['node'])) {
                msg("{$node_data['node_group']} {$node_data['title']} (id={$node_data['id']}) has been updated");
            } else {
                msg("Cannot update {$node_data['node_group']} {$node_data['title']} (id={$node_data['id']})", 'error');
            }
        }

        // parent node
        $this->tpl->assign('PARENT', $parent_data['title']);
        $this->tpl->assign('NODE', $node_data);

        parent::parseTemplate();

        return true;
    }
}   

