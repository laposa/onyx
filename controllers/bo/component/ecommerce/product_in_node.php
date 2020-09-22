<?php
/** 
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Ecommerce_Product_In_Node extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        require_once('models/ecommerce/ecommerce_product.php');
        
        $Node = new common_node();
        $Product = new ecommerce_product();
        
        $product_id =  $this->GET['id'];
        
        /**
         * find product homepage
         */
         
        $product_homepage = $Product->getProductHomepage($product_id);
        
        /**
         * allow to insert new
         */
         
        if (!is_array($product_homepage) && !is_numeric($this->GET['add_to_parent'])) {
            $this->tpl->parse('content.not_exists');
        }
        
        /**
         * move page if requested
         */
         
        if (is_numeric($this->GET['add_to_parent'])) {
            if (is_array($product_homepage )) {
                //moving
                $product_homepage = $this->moveProductNode($product_id, $this->GET['add_to_parent']);
            } else {
                //insert new
                $product_homepage = $this->insertNewProductToNode($product_id, $this->GET['add_to_parent']);
            }
            
            
        }
        
        
        /**
         * display product homepage detail
         */
         
        if (is_array($product_homepage)) {
            
            //parent detail
            $parent_detail = $Node->detail($product_homepage['parent']);
            $this->tpl->assign("PARENT_DETAIL", $parent_detail);
            
            //breadcrumb
            $_Onyx_Request = new Onyx_Request("component/breadcrumb~id={$product_homepage['id']}:create_last_link=1~");
            $this->tpl->assign('BREADCRUMB', $_Onyx_Request->getContent());
            
            //children node list
            $_Onyx_Request = new Onyx_Request("bo/component/node_list~id={$product_homepage['id']}:node_group=content~");
            $this->tpl->assign('NODE_LIST', $_Onyx_Request->getContent());
            
            //parse
            $this->tpl->parse('content.product_node');
        }
        
        return true;
    }
    
    /**
     * insert product to node
     */
    
    function insertNewProductToNode($product_id, $parent_id) {
    
        if (!is_numeric($product_id)) return false;
        if (!is_numeric($parent_id)) return false;
        
        $Node = new common_node();
        $Product = new ecommerce_product();
        
        /**
         * get product detail
         */
         
        $product_detail = $Product->detail($product_id);
         
        /**
         * prepare node data
         */
         
        $product_node['title'] = $product_detail['name'];
        $product_node['parent'] = $parent_id;
        $product_node['parent_container'] = 0;
        $product_node['node_group'] = 'page';
        $product_node['node_controller'] = 'product';
        $product_node['content'] = $product_id;
        //$product_node['layout_style'] = $Node->conf['page_product_layout_style'];
        //this need to be updated on each product update
        $product_node['priority'] = $product_detail['priority'];
        $product_node['publish'] = $product_detail['publish'];
        
        /**
         * insert node
         */
         
        if ($product_homepage = $Node->nodeInsert($product_node)) {
            msg("Product has been added into the node", 'ok');
            return $product_homepage;
        } else {
            msg("Can't add product to node.");
            return false;
        }
    }
    
    /**
     * move product node
     */
     
    function moveProductNode($product_id, $parent_id) {
    
        if (!is_numeric($product_id)) return false;
        if (!is_numeric($parent_id)) return false;
        
        $Node = new common_node();
        $Product = new ecommerce_product();
        
        /**
         * get current detail
         */
         
        $product_homepage = $Product->getProductHomepage($product_id);
         
        /**
         * modify node data
         */
        
        $product_homepage['parent'] = $parent_id;
        
        if ($Node->nodeUpdate($product_homepage)) {
            msg("Product node has been updated", 'ok');
            return $product_homepage;
        } else {
            msg("Can't update product node.");
            return false;
        }
        
    }
}
