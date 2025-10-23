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
        $node_data = $node->nodeDetail($this->GET['node_id']);

        // display title
        if (!is_numeric($node_data['display_title'])) $node_data['display_title'] = $GLOBALS['onyx_conf']['global']['display_title'];

        if ($node_data['display_title'] == 1) {
            $node_data['display_title_check'] = 'checked="checked"';
        } else {
            $node_data['display_title_check'] = '';
        }

        // save
        if (isset($_POST['save'])) {
            // TODO: messages
            if($node->nodeUpdate($_POST['node'])) {
                msg("{$node_data['node_group']} (id={$node_data['id']}) has been updated");
                // header('HX-Trigger: {"nodeUpdated":{"init" :"false"}}');
            } else {
                msg("Cannot update node {$node_data['node_group']} (id={$node_data['id']})", 'error');
            }
        }
        
        $this->tpl->assign('NODE', $node_data);

        parent::parseTemplate();

        return true;
    }

}   

