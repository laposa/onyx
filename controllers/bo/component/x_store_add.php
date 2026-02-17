<?php
/** 
 * Copyright (c) 2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


require_once('controllers/bo/component/x.php');
require_once('models/common/common_node.php');
require_once('models/ecommerce/ecommerce_store.php');

class Onyx_Controller_Bo_Component_X_Store_Add extends Onyx_Controller_Bo_Component_X {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        $node = new common_node();
        $store = new ecommerce_store();

        $node_data = $node->detail($this->GET['node_id']);
        $store_data = $_POST['store'];

        if ($_POST['save'] ?? false) {
            if($id = $store->insertStore($store_data)) {
                $node_data['content'] = $id;
                if($node->nodeUpdate($node_data)) {
                    msg("Store {$store_data['title']} has been added.");
                } else {
                    msg("Store {$store_data['title']} has been created but couldn't be assigned to page {$node_data['title']}.", 'error');
                }
            } else {
                msg("Adding of store failed.", 'error');
            }
        }

        return true;
    }
}
