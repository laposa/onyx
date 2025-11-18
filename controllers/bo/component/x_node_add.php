<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/x.php');

class Onyx_Controller_Bo_Component_X_Node_Add extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * initialize
         */

        require_once('models/common/common_node.php');
        
        $node_data = $_POST['node'] ?? null;
        $position = $_POST['position'] ?? null;
        
        $Node = new common_node();
        
        if ($_POST['save'] ?? false) {

            if ($node_data['parent'] == $Node->conf['id_map-homepage'] && $node_data['node_group'] == 'page') {
                
                $home_page_data = $Node->getDetail($Node->conf['id_map-homepage']);
                $node_data['parent'] = $home_page_data['parent'];
                $home_page_parent_data = $Node->getDetail($home_page_data['parent']);
                msg("Inserting page under {$home_page_parent_data['title']}");
            }
            
            /**
             * insert a new node
             */
            if($id = $Node->nodeInsert($node_data, $position)) {
                // TODO: messages and menu update
                msg(ucfirst($node_data['node_group']) . " ". $node_data['title'] . " has been added.");
                header('HX-Trigger: {"nodeUpdated":{"init" :"false"}}');
            }
        }
        
        return true;
    }
}
