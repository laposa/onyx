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
        $this->Node = new common_node();
        $this->node_data = $this->Node->nodeDetail($this->GET['node_id']);
        if ($this->node_data) $this->tpl->assign('NODE', $this->node_data);

        $this->tpl->assign('NODE_URL', translateURL("page/".$this->GET['node_id']));
        
        // SLUG
        $CommonUriMapping = new common_uri_mapping();
        $this->tpl->assign('NODE_URL_LAST_SEGMENT', $CommonUriMapping->cleanTitle($this->node_data['title']));
        
        parent::parseTemplate();

        return true;
    }
}   

