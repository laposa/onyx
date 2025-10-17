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
        $Product = new ecommerce_product();
        $product_data = $Product->productDetail($this->GET['node_id']);
        if ($product_data) $this->tpl->assign('PRODUCT', $product_data);

        parent::parseTemplate();

        return true;
    }
}   

