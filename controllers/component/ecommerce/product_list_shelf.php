<?php
/**
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/product_list.php');

class Onxshop_Controller_Component_Ecommerce_Product_List_Shelf extends Onxshop_Controller_Component_Ecommerce_Product_List {

    /**
     * main action
     */
     
    public function mainAction() {
    
        return $this->processProductList();
    
    }
    
    
}
