<?php
/** 
 * Copyright (c) 2005-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Ecommerce_Product_Variety_Add extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        $product_data = $_POST['product'];
        
        if (!isset($product_data['variety']['product_id'])) $product_data['variety']['product_id'] = $product_data['id'];
        
        require_once('models/ecommerce/ecommerce_product_variety.php');
        $Product_variety = new ecommerce_product_variety();
        
        $this->tpl->assign("VARIETY_CONF", $Product_variety->conf);
        
        if ($_POST['save']) {
            
            if($id = $Product_variety->insertFullVariety($product_data['variety'])) {
                
                msg("Product variety id=$id has been added.");
                
                //empty
                $product_data = array();
                
            } else {
                msg ("Can't add the product variety for product id={$product_data['variety']['product_id']}. Is your product SKU unique? Did you fill in stock value?");
            }
        }
        
        $this->tpl->assign('PRODUCT', $product_data);

        return true;
    }
    
}
