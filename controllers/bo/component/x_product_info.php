<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/x.php');
require_once('models/ecommerce/ecommerce_product.php');

class Onyx_Controller_Bo_Component_X_Product_Info extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */
     
    public function mainAction() {

        // get details
        $product = new ecommerce_product();
        $product_data = $product->productDetail($this->GET['node_id']);

        // save
        if (isset($_POST['save'])) {
            // TODO: messages
            if($product->updateProduct($_POST['product'])) {
                msg("{$product_data['name']} (id={$product_data['id']}) has been updated");
                // header('HX-Trigger: {"nodeUpdated":{"init" :"false"}}');
            } else {
                msg("Cannot update node {$product_data['name']} (id={$product_data['id']})", 'error');
            }
        }

        if ($product_data) $this->tpl->assign('PRODUCT', $product_data);

        parent::parseTemplate();

        return true;
    }
}   

