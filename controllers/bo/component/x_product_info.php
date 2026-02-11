<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/x.php');
require_once('models/ecommerce/ecommerce_product.php');
require_once('models/common/common_node.php');

class Onyx_Controller_Bo_Component_X_Product_Info extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */
     
    public function mainAction() {

        // node details
        $node = new common_node();
        $node_data = $node->detail($this->GET['node_id']);

        // get product details from node content in order to avoid issues with refreshing & check whether product exists
        $product = new ecommerce_product();
        $product_data = $product->productDetail(is_numeric($node_data['content']) ? $node_data['content'] : null);
        
        // save
        if (isset($_POST['save'])) {
            if($product->updateProduct($_POST['product'])) {
                msg("{$product_data['name']} (id={$product_data['id']}) has been updated");
            } else {
                msg("Cannot update node {$product_data['name']} (id={$product_data['id']})", 'error');
            }
        }
        
        $this->tpl->assign('NODE', $node_data);

        if ($product_data) {
            $this->tpl->assign('PRODUCT', $product_data);
            parent::parseTemplate();
        } else $this->tpl->parse("content.missing_product");

        return true;
    }
}   

