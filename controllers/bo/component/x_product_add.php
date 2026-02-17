<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


require_once('models/ecommerce/ecommerce_product.php');
require_once('models/ecommerce/ecommerce_product_variety.php');
require_once('controllers/bo/component/x.php');
require_once('models/common/common_node.php');

class Onyx_Controller_Bo_Component_X_Product_Add extends Onyx_Controller_Bo_Component_X {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        $node = new common_node();
        $product = new ecommerce_product();
        $product_variety = new ecommerce_product_variety();

        //nodeDetail causes error while update - something about author_detail relation
        $node_data = $node->detail($this->GET['node_id']);

        if ($_POST['save'] ?? false) {

            $product_data = $_POST['product'];

            if($id = $product->insertFullProduct($product_data)) {
                msg("Product has been added.");

                $node_data['content'] = $id;
                if($node->nodeUpdate($node_data)) {
                    msg("Successfully linked product to this page.");
                } else {
                    msg("Could not connect created product to this page.");
                }
            } else {
                msg("Product has not been added.");
            }
        } else {
            $product_data = array();
            $product_data['variety'] = array();
            $product_data['variety']['price'] = array();
            $product_data['variety']['name'] = 'Item';
            $product_data['variety']['weight_gross'] = 0;
            $product_data['variety']['stock'] = 999;
            $product_data['variety']['price']['value'] = 0;
        }

        $this->tpl->assign("VARIETY_CONF", $product_variety->conf);

        return true;
    }
}
