<?php
/** 
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Price extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/ecommerce/ecommerce_price.php');
        $Price = new ecommerce_price();
        
        /**
         * find selected currency code
         */
        /*
        if (isset($_POST['client']['customer']['currency_code'])) {
            $currency_code = $_POST['client']['customer']['currency_code'];
        } else if (isset($_SESSION['client']['customer']['currency_code'])) {
            $currency_code = $_SESSION['client']['customer']['currency_code'];
        } else {
            $currency_code = $Price->conf['default_currency'];
        }*/
        
        $currency_code = GLOBAL_LOCALE_CURRENCY;
        
        /**
         * get latest price for selected currency code
         */
        
        $price_common = $Price->getLastPriceForVariety($this->GET['product_variety_id'], $currency_code, 'common');
        
        $pt = array();
        $pt['common'] = $price_common;
        $pt['discount'] = array();
        
        /**
         * Display
         */
        
        $this->tpl->assign("PRICE", $pt);
            
        if ($pt['discount']['value'] > 0) {
            $this->tpl->parse('content.price_discount');
        } else {
            $this->tpl->parse('content.price_common');
        }

        return true;
    }
}
