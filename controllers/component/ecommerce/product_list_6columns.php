<?php
/**
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/product_list.php');

class Onyx_Controller_Component_Ecommerce_Product_List_6columns extends Onyx_Controller_Component_Ecommerce_Product_List {

    /**
     * main action
     */
     
    public function mainAction() {
    
        return $this->processProductList();
    
    }
    
    /**
     * process items
     */
     
    function processItems($product_list, $image_width, $from, $per_page, $divide_after = 6) {
        $image_width = 160;
        return $this->_displayItems($product_list, $image_width, $from, $per_page, $divide_after);
    }

}
