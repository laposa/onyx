<?php
/** 
 * Copyright (c) 2011-2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */
require_once('controllers/bo/component/x.php');
require_once('models/ecommerce/ecommerce_product.php');

class Onyx_Controller_Bo_Component_X_Product_Other_Data extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */
     
    public function mainAction() {

         // get details
        $Product = new ecommerce_product();
        $product_data = $Product->productDetail($this->GET['node_id']);
        if ($product_data) $this->tpl->assign('PRODUCT', $product_data);
    
        /**
         * other data (attributes) list
         */

        $template = (isset($_GET['edit']) && $_GET['edit'] == 'true') ? 'edit' : 'preview';
        
        if (is_array($product_data['other_data'])) {
            foreach ($product_data['other_data'] as $key=>$value) {
                $note['key'] = $key;
                $note['value'] = $value;
                if ($note['key'] != '') {
                    $this->tpl->assign('OTHER_DATA', $note);
                    $this->tpl->parse("content.{$template}.other_data.item");
                }
            }
            if (count($product_data['other_data']) > 0) $this->tpl->parse("content.{$template}.other_data");
        }

        parent::parseTemplate();

        return true;
    }
}
