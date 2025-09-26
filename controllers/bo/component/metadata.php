<?php
/** 
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component.php');

class Onyx_Controller_Bo_Component_Metadata extends Onyx_Controller_Bo_Component {

    /**
     * main action
     */
     
    public function mainAction() {

        parent::assignNodeData();

        $this->tpl->assign('NODE_URL', translateURL("page/".$this->GET['node_id']));
        
        // SLUG
        $CommonUriMapping = new common_uri_mapping();
        $this->tpl->assign('NODE_URL_LAST_SEGMENT', $CommonUriMapping->cleanTitle($this->node_data['title']));
        
        parent::parseTemplate();

        return true;
    }
}   

