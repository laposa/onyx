<?php
/** 
 * Copyright (c) 2006-2012 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */
 
class Onxshop_Controller_Component_Ecommerce_Product_Other_Data extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/ecommerce/ecommerce_product.php');
        $Product = new ecommerce_product();
        
        $product_detail = $Product->getDetail($this->GET['id']);
    
        if (is_array($product_detail['other_data'])) {
        
            foreach ($product_detail['other_data'] as $key=>$value) {
        
                //format key
                $key = preg_replace("/required_/","",$key);
                $key = preg_replace("/_/"," ",$key);
                $key = ucfirst($key);
    
                //prepare array for template
                $note['key'] = $key;
                $note['value'] = $value;
                
                //assign to template
                if (trim($note['value']) != '') {
                    $this->tpl->assign('OTHER_DATA', $note);
                    $this->tpl->parse('content.other_data.item');
                    $show_other_data = 1;
                }
            }
            
            //display
            if (count($product_detail['other_data']) > 0) $this->tpl->parse('content.other_data');
        }

        return true;
    }
}

