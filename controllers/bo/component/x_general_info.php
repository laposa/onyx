<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/x.php');
require_once('models/common/common_node.php');
class Onyx_Controller_Bo_Component_X_General_Info extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */

    public function mainAction() {

        // get details
        $node = new common_node();
        $node_data = $node->nodeDetail($this->GET['node_id'] ?? $_POST['node']['id']);

        // display title
        if (!is_numeric($node_data['display_title'])) $node_data['display_title'] = $GLOBALS['onyx_conf']['global']['display_title'];

        if ($node_data['display_title'] == 1) {
            $node_data['display_title_check'] = 'checked="checked"';
        } else {
            $node_data['display_title_check'] = '';
        }

        // save
        if (isset($_POST['save'])) {
            if($node->nodeUpdate($_POST['node'])) {
                sendNodeUpdateResponse("{$node_data['node_group']} <b>{$node_data['title']}</b>({$node_data['id']}) has been updated", 200, 'Update successful');
            } else {
                sendNodeUpdateResponse("Cannot update node {$node_data['node_group']} <b>{$node_data['title']}</b>({$node_data['id']})", 500, 'Update failed');
            }

            //trigger page refresh if node type changed
            if($_POST['node']['node_group'] != $node_data['node_group'] || $_POST['node']['node_controller'] != $node_data['node_controller']) {
                header("HX-Trigger: pageRefresh");
            }
        }

        $node_type = 
            ucwords(str_replace(['-', '_'], ' ', $node_data['node_group'])) . ' - ' . 
            ucwords(str_replace(['-', '_'], ' ', $node_data['node_controller']));
        $this->tpl->assign('NODE_TYPE', $node_type);

        $this->tpl->assign('NODE', $node_data);

        parent::parseTemplate();

        return true;
    }
}   

