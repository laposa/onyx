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
        
        // SLUG
        $common_uri_mapping = new common_uri_mapping();
        $this->tpl->assign('NODE_URL_LAST_SEGMENT', $common_uri_mapping->cleanTitle($node_data['title']));

        // Check if the URL already exists
        $node_url = $common_uri_mapping->generateSingleURIFullPath($node_data);
        $existing_url = $common_uri_mapping->getExistingURI($node_url);

        $this->tpl->assign('NODE_URL', $node_url);

        if(is_array($existing_url) && count($existing_url) > 0 && $existing_url[0]['node_id'] != $node_data['id']) {
            $this->tpl->assign('CONFLICTING_NODE', $existing_url[0]['node_id']);
            $this->tpl->parse($_GET['edit'] == 'true' ? "content.edit.conflict" : "content.preview.conflict");
        }
        
        //save
        if (isset($_POST['save'])) {
            $save_data = $_POST['node'];
            $save_data['title'] = $node_data['title'];
            $save_data['node_group'] = $node_data['node_group']; // make sure redirect is generated correctly
            $node->nodeUpdate($save_data);
            return true;
        }

        if ($node_data) $this->tpl->assign('NODE', $node_data);

        parent::parseTemplate();

        return true;
    }
}   

