<?php
/** 
 * Copyright (c) 2005-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Ecommerce_Product_Edit extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * initialize
         */
         
        require_once('models/ecommerce/ecommerce_product.php');
        $Product = new ecommerce_product();
        
        /**
         * save
         */
         
        if ($_POST['save']) {
        
            $product_data = $_POST['product'];
            
            if ($id = $Product->updateProduct($product_data)) {
                
                /**
                 * forward to product list main page and exit
                 */ 
                
                onyxGoTo("/backoffice/products");
                
                return true;
                
            }
            
        }
        
        /**
         * product detail
         */
         
        $product = $Product->detail($this->GET['id']);
        $product['publish'] = ($product['publish'] == 1) ? 'checked="checked" ' : '';
        $this->tpl->assign('PRODUCT', $product);

        return true;
    }
}   
            
