<?php
/** 
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Ecommerce_Relation_Product_Related extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/ecommerce/ecommerce_product_to_product.php');
        require_once('models/ecommerce/ecommerce_product.php');
        
        $PtP = new ecommerce_product_to_product();
        $Product = new ecommerce_product();
        
        $product_id = $this->GET['id'];
        
        $ptp_data = array();
        $ptp_data['product_id'] = $product_id;
        
        /**
         * saving
         */
         
        if (is_array($_POST['product_related'] ?? null)) {
        
            $current = $PtP->listing("product_id = $product_id");
        
            foreach ($current as $c) {
                $PtP->delete($c['id']);
            }
        
            foreach ($_POST['product_related'] as $to_id) {
                if (is_numeric($to_id)) {
                    $ptp_data['related_product_id'] = $to_id;
                    $PtP->insert($ptp_data);
                }
            }
        }
        
        /**
         * listing
         */
         
        $current = $PtP->listing("product_id = $product_id");
        foreach ($current as $c) {
            $detail = $Product->detail($c['related_product_id']);
            if ($detail['publish'] == 0) $detail['class'] = "class='disabled'";
            $this->tpl->assign("CURRENT", $detail);
            $this->tpl->parse("content.ptn");
        }

        return true;
    }
}

