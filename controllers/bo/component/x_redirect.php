<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/x.php');
require_once('models/common/common_node.php');
class Onyx_Controller_Bo_Component_X_Redirect extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */
     
    public function mainAction() {

        // get details
        $node = new common_node();
        $node_data = $node->nodeDetail($this->GET['node_id']);
        if ($node_data) $this->tpl->assign('NODE', $node_data);

        //save
        if (isset($_POST['save'])) {
            $node_data = $_POST['node']; 

            // TODO: messages
            if($node->nodeUpdate($node_data)) {
                msg("{$node_data['node_group']} (id={$node_data['id']}) has been updated");
                // header('HX-Trigger: {"nodeUpdated":{"init" :"false"}}');
            } else {
                msg("Cannot update node {$node_data['node_group']} (id={$node_data['id']})", 'error');
            }
        }
        
        parent::parseTemplate();
        return true;
    }

}   

