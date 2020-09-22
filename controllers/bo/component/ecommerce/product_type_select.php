<?php
/** 
 * Copyright (c) 2005-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Component_Ecommerce_Product_Type_Select extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * input
         */
         
        if (is_numeric($this->GET['product_variety_id'])) $product_variety_id = $this->GET['product_variety_id'];
        else $product_variety_id = false;
        
        /**
         * initialize
         */
         
        require_once('models/ecommerce/ecommerce_product_variety.php');
        require_once('models/ecommerce/ecommerce_product_type.php');
        $ProductVariety = new ecommerce_product_variety();
        $ProductType = new ecommerce_product_type();
        
        /**
         * get product variety detail if requested
         */
         
        if (is_numeric($product_variety_id)) $product_variety = $ProductVariety->detail($product_variety_id);
        
        /**
         * prepare product type id (either for requested product variety or default one)
         */
         
        if (is_numeric($product_variety['product_type_id'])) $product_type_id = $product_variety['product_type_id'];
        else $product_type_id = $ProductType->conf['default_id'];
        
        /**
         * listing published items
         */
         
        $types = $ProductType->listing("publish = 1");
        
        foreach ($types as $type) {
            $this->tpl->assign('TYPE', $type);
            if ($type['id'] == $product_type_id) {
                $this->tpl->assign('SELECTED', 'selected="selected"');
            } else {
                $this->tpl->assign('SELECTED', '');
            }
            $this->tpl->parse('content.type');
        }

        return true;
    }
}
