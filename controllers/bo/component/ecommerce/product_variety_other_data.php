<?php
/** 
 * Copyright (c) 2012 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * this is by default empty, but allows to create project specific other_data in local template
 * simply create 
 */
 
class Onxshop_Controller_Bo_Component_Ecommerce_Product_Variety_Other_Data extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        if (is_numeric($this->GET['id'])) $variety_id = $this->GET['id'];
        else return false;
        
        require_once('models/ecommerce/ecommerce_product_variety.php');
        $ProductVariety = new ecommerce_product_variety();
        
        //keep the same naming as in product_variety_edit controller
        $product = array();
        $product['variety'] = $ProductVariety->getDetail($variety_id);
                
        $this->tpl->assign('PRODUCT', $product);

        return true;
    }
}

