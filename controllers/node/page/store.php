<?php
/**
 * Copyright (c) 2013-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('models/common/common_node.php');
require_once('controllers/node/page/default.php');
require_once('models/ecommerce/ecommerce_store.php');
require_once('models/ecommerce/ecommerce_store_taxonomy.php');

class Onxshop_Controller_Node_Page_Store extends Onxshop_Controller_Node_Page_Default {

    /**
     * main action
     */
    public function mainAction()
    {
        parent::mainAction();
    
        // GET.store_id came from parseContentTagsBeforeHook()
        $this->storeDetail($this->GET['store_id']);
                
        return true;
    }
    
    /**
     * hook before parsing
     */
     
    public function parseContentTagsBeforeHook()
    {

        parent::parseContentTagsBeforeHook();

        /**
         * pass GET.store_id into template
         */
         
        $Node = new common_node();
        $this->node_data = $Node->nodeDetail($this->GET['id']);
        $this->GET['store_id'] = $this->node_data['content'];
        
        /**
         * pass GET.store_type_id into template
         */
         
        $Store= new ecommerce_store();
        $this->store_detail = $Store->getDetail($this->GET['store_id']);
        
        $this->GET['store_type_id'] = $this->store_detail['type_id'];
        
        /**
         * pass GET.taxonomy_tree_id into template
         */
         
        $Store_Taxonomy = new ecommerce_store_taxonomy();
        $taxonomy_ids = (array) $Store_Taxonomy->getRelationsToStore($this->GET['store_id']);
        
        $this->GET['taxonomy_tree_id'] = implode(",", $taxonomy_ids);
        
    }

    /**
     * storeDetail
     */
     
    public function storeDetail($store_id) {
        
        if (!is_numeric($store_id)) return false;
        
        $Store= new ecommerce_store();
        //$store_detail = $Store->getDetail($store_id);
        $store_detail = $this->store_detail;

        if ($store_detail) {
        
            /**
             * get taxonomy_class
             */
             
            $related_taxonomy = $Store->getRelatedTaxonomy($store_id);
            $store_detail['taxonomy_class'] = $this->createTaxonomyClass($related_taxonomy);
            
            /**
             * save product taxonomy_class to registry
             */
            
            $this->saveBodyCssClass($store_detail['taxonomy_class']);
            
        
            
            $this->tpl->assign("STORE", $store_detail);

        }

        return true;
    }
}
