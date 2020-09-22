<?php
/** 
 * DEPRICATED, modified for transition to single product_in_node and categories
 *
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Ecommerce_Relation_Product_In_Nodes extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        require_once('models/ecommerce/ecommerce_product.php');
        
        $Node = new common_node();
        $Product = new ecommerce_product();
        
        $product_id =  $this->GET['id'];
        
        
        //listing
        if (is_numeric($product_id)) {
            $current = $Product->findProductInNode($product_id);
            
            $this->tpl->assign('NODE', $Node->detail($current[0]['id']));
            
            foreach ($current as $key=>$node_data) {
                //don't display homepage
                if ($key > 0) {
                    if ($node_data['publish'] == 0) $node_data['class'] = "class='disabled'";
                    $this->tpl->assign("ITEM", $node_data);
                    $_Onyx_Request = new Onyx_Request("component/breadcrumb~id={$node_data['id']}:create_last_link=1~");
                    $this->tpl->assign('BREADCRUMB', $_Onyx_Request->getContent());
                    $this->tpl->parse("content.multiple.item");
                }
            }
            
            if (count($current) > 1) $this->tpl->parse('content.multiple');
        }

        return true;
    }
}
