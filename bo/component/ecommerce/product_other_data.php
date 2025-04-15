<?php
/** 
 * Copyright (c) 2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Component_Ecommerce_Product_Other_Data extends Onyx_Controller {

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
         * get detail
         */
         
        $product = $Product->detail($this->GET['id']);
        
        $this->tpl->assign('PRODUCT', $product);
        
        /**
         * other data (attributes) list
         */
         
        $product['other_data'] = unserialize($product['other_data'] ?? '');
        
        if (is_array($product['other_data'])) {
            foreach ($product['other_data'] as $key=>$value) {
                $note['key'] = $key;
                $note['value'] = $value;
                if ($note['key'] != '') {
                    $this->tpl->assign('OTHER_DATA', $note);
                    $this->tpl->parse('content.other_data.item');
                }
            }
            if (count($product['other_data']) > 0) $this->tpl->parse('content.other_data');
        }

        return true;
        
    }
}
