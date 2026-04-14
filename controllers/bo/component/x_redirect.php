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
        $node_data = $node->nodeDetail($this->GET['node_id'] ?? $_POST['node']['id']);
        if ($node_data) $this->tpl->assign('NODE', $node_data);

        $readable_link = $node_data['component']['href'];

        if(str_starts_with($node_data['component']['href'], '/page')) {
            $readable_link = translateURL(ltrim($node_data['component']['href'], '/'));
        }

        //save
        if (isset($_POST['save'])) {

            $node->nodeUpdate($_POST['node']);
            return true;
            
        }

        $this->tpl->assign('PRETTY_LINK', $readable_link);
        
        parent::parseTemplate();
        return true;
    }

}   

