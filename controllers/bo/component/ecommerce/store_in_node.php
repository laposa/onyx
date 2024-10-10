<?php
/** 
 * Copyright (c) 2010-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Ecommerce_Store_In_Node extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        require_once('models/ecommerce/ecommerce_store.php');
        
        $Node = new common_node();
        $Store = new ecommerce_store();
        
        $store_id =  $this->GET['id'];
        
        /**
         * find store homepage
         */
         
        $store_homepage = $Store->getStoreHomepage($store_id);
        
        /**
         * allow to insert new
         */
         
        if (!is_array($store_homepage) && !is_numeric($this->GET['add_to_parent'])) {
            $this->tpl->parse('content.not_exists');
        }
        
        /**
         * move page if requested
         */
         
        if (is_numeric($this->GET['add_to_parent'] ?? null)) {
            if (is_array($store_homepage )) {
                //moving
                $store_homepage = $this->moveStoreNode($store_id, $this->GET['add_to_parent']);
            } else {
                //insert new
                $store_homepage = $this->insertNewStoreToNode($store_id, $this->GET['add_to_parent']);
            }
            
            
        }
        
        
        /**
         * display store homepage detail
         */
         
        if (is_array($store_homepage)) {
            
            //parent detail
            $parent_detail = $Node->detail($store_homepage['parent']);
            $this->tpl->assign("PARENT_DETAIL", $parent_detail);
            
            //breadcrumb
            $_Onyx_Request = new Onyx_Request("component/breadcrumb~id={$store_homepage['id']}:create_last_link=1~");
            $this->tpl->assign('BREADCRUMB', $_Onyx_Request->getContent());
            
            //children node list
            $_Onyx_Request = new Onyx_Request("bo/component/node_list~id={$store_homepage['id']}:node_group=content~");
            $this->tpl->assign('NODE_LIST', $_Onyx_Request->getContent());
            
            //parse
            $this->tpl->parse('content.store_node');
        }
        
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
            msg("Store has been added into the node", 'ok');
            return $store_homepage;
        } else {
            msg("Can't add store to node.");
            return false;
        }
    }
    
    /**
     * move store node
     */
     
    function moveStoreNode($store_id, $parent_id) {
    
        if (!is_numeric($store_id)) return false;
        if (!is_numeric($parent_id)) return false;
        
        $Node = new common_node();
        $Store = new ecommerce_store();
        
        /**
         * get current detail
         */
         
        $store_homepage = $Store->getStoreHomepage($store_id);
         
        /**
         * modify node data
         */
        
        $store_homepage['parent'] = $parent_id;
        
        if ($Node->nodeUpdate($store_homepage)) {
            msg("Store node has been updated", 'ok');
            return $store_homepage;
        } else {
            msg("Can't update store node.");
            return false;
        }
        
    }
}
