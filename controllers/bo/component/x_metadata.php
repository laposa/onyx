<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/x.php');
require_once('models/common/common_node.php');

class Onyx_Controller_Bo_Component_X_Metadata extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */
     
    public function mainAction() {

        // get details
        $node = new common_node();
        $node_data = $node->nodeDetail($this->GET['node_id'] ?? $_POST['node']['id']);

        $this->tpl->assign('NODE_URL', translateURL("page/".$this->GET['node_id']));
        
        // SLUG
        $CommonUriMapping = new common_uri_mapping();
        $this->tpl->assign('NODE_URL_LAST_SEGMENT', $CommonUriMapping->cleanTitle($node_data['title']));
        
        //save
        if (isset($_POST['save'])) {
            if($node->nodeUpdate($_POST['node'])) {
                msg("{$node_data['node_group']} (id={$node_data['id']}) has been updated");
            } else {
                msg("Cannot update node {$node_data['node_group']} (id={$node_data['id']})", 'error');
            }
        }

        if ($node_data) $this->tpl->assign('NODE', $node_data);

        parent::parseTemplate();

        return true;
    }
}   

