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

        $node_data['display_title_check'] = $node_data['display_title'] == 1 ? 'checked="checked"' : '';
        $node_data['display_secondary_navigation_check'] = $node_data['display_secondary_navigation'] == 1 ? 'checked="checked"' : '';

        // save
        if (isset($_POST['save'])) {

            $_POST['node']['display_title'] = $_POST['node']['display_title'] ? 1 : 0;
            $_POST['node']['display_secondary_navigation'] = $_POST['node']['display_secondary_navigation'] ? 1 : 0;
            if($node->nodeUpdate($_POST['node'])) {
                msg("{$node_data['node_group']} {$node_data['title']} (id={$node_data['id']}) has been updated");
            } else {
                msg("Cannot update {$node_data['node_group']} {$node_data['title']} (id={$node_data['id']})", 'error');
            }

            //trigger page refresh if node type changed
            if($_POST['node']['node_group'] != $node_data['node_group'] || $_POST['node']['node_controller'] != $node_data['node_controller']) {
                header('HX-Trigger: {"loadDetail":{"nodeId" :"'.$node_data['id'].'"}}');
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

