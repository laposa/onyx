<?php
/** 
 * Copyright (c) 2009-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Product_Add_Quick extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/ecommerce/ecommerce_product.php');
        require_once('models/ecommerce/ecommerce_product_variety.php');
        require_once('models/ecommerce/ecommerce_price.php');
    
        $Product = new ecommerce_product();
        $Product_variety = new ecommerce_product_variety();
        $Price = new ecommerce_price();
    
        $this->tpl->assign("VARIETY_CONF", $Product_variety->conf);
    
        if ($_POST['save']) {

            $product_data = $_POST['product'];
            
            /**
             * add product
             */
             
            if($product_id = $Product->insertFullProduct($product_data)) {
                msg("Product id=$product_id interted.");
                
                //TODO: implement two options: 1. save end this, 2. save and add another
                onxshopGoTo("backoffice/products/{$product_id}/edit");
                //empty
                $product_data = array();
            } else {
                msg("Product add has failed.", 'error');
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
        
        $this->tpl->assign('PRODUCT', $product_data);

        return true;
    }
}
