<?php
/** 
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component.php');
require_once('models/ecommerce/ecommerce_product.php');

class Onyx_Controller_Bo_Component_Product_Info extends Onyx_Controller_Bo_Component {

    /**
     * main action
     */

    public $Product;
    public $product_data;
     
    public function mainAction() {

        parent::assignNodeData();

        $this->Product = new ecommerce_product();
        $this->product_data = $this->Product->productDetail($this->node_data['content']);

        $this->tpl->assign('PRODUCT', $this->product_data);

        parent::parseTemplate();

        return true;
    }
}   

