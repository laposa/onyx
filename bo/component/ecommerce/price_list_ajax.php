<?php
/** 
 * Copyright (c) 2006-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Component_Ecommerce_Price_List_Ajax extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        require_once('models/ecommerce/ecommerce_price.php');
        $Price = new ecommerce_price();
        
        $price_list = $Price->getPriceList($this->GET['product_variety_id']);
        $this->tpl->assign('CONTENT', json_encode($price_list));

        return true;
    }
}
