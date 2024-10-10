<?php
/** 
 * Copyright (c) 2005-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Ecommerce_Product_Add extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/ecommerce/ecommerce_product.php');
        $Product = new ecommerce_product();
        
        $product_data = $_POST['product'];
        
        if ($_POST['save'] ?? false) {
            if($id = $Product->insertProduct($product_data)) {
                msg("Product has been added.");
                onyxGoTo("backoffice/products/$id/variety_add");
            } else {
                msg("Adding of Product Failed.", 'error');
            }
        }
        $this->tpl->assign('PRODUCT', $product_data);

        return true;
    }
}
