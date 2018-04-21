<?php
/** 
 * Copyright (c) 2005-2011 Onxshop Ltd (https://onxshop.com)
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

                $store_homepage = $this->insertNewStoreToNode($id, $page_node_id);

                msg("Store has been added.");
                onxshopGoTo("backoffice/stores/$id/edit");
            } else {
                msg("Adding of Store Failed.", 'error');
            }
        }

        $store_data['page_node_id'] = (int) $_SESSION['active_pages'][0];
        $this->tpl->assign('STORE', $store_data);

        return true;
    }

    /**
     * insert store to node
     */
    
    function insertNewStoreToNode($store_id, $parent_id) {
    
        if (!is_numeric($store_id)) return false;
        if (!is_numeric($parent_id)) return false;
        
        $Node = new common_node();
        $Store = new ecommerce_store();
        
        /**
         * get store detail
         */
         
        $store_detail = $Store->detail($store_id);
         
        /**
         * prepare node data
         */
         
        $store_node['title'] = $store_detail['title'];
        $store_node['parent'] = $parent_id;
        $store_node['parent_container'] = 0;
        $store_node['node_group'] = 'page';
        $store_node['node_controller'] = 'store';
        $store_node['content'] = $store_id;
        //$store_node['layout_style'] = $Node->conf['page_store_layout_style'];
        //this need to be updated on each store update
        $store_node['priority'] = $store_detail['priority'];
        $store_node['publish'] = $store_detail['publish'];

        /**
         * insert node
         */
         
        if ($store_homepage = $Node->nodeInsert($store_node)) {
            return $store_homepage;
        } else {
            msg("Can't add store to node.");
            return false;
        }
        
    }

}
