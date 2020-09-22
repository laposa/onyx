<?php
/** 
 * Copyright (c) 2005-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_node.php');
require_once('models/ecommerce/ecommerce_store.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Store_Add extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        $Store = new ecommerce_store();
        
        $store_data = $_POST['store'];
        $page_node_id = $store_data['page_node_id'];
        unset($store_data['page_node_id']);
        
        if ($_POST['save']) {
            if($id = $Store->insertStore($store_data)) {

                $store_homepage_node_id = $Store->insertNewStoreToNode($id, $page_node_id);

                msg("Store has been added.");
                onxshopGoTo("backoffice/stores/$id/edit");
            } else {
                msg("Adding of store failed.", 'error');
            }
        }

        $store_data['page_node_id'] = (int) $_SESSION['active_pages'][0];
        $this->tpl->assign('STORE', $store_data);

        return true;
    }

}
