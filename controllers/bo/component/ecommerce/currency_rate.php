<?php
/** 
 * Copyright (c) 2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Currency_Rate extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        require_once('models/international/international_currency.php');
        
        $allowed_locales = array('en_GB.UTF-8', 'en_US.UTF-8', 'en_IE.UTF-8', 'cs_CZ.UTF-8', 'de_DE.UTF-8', 'en_AU.UTF-8', 'ja_JP.UTF-8', 'en_CA.UTF-8', 'en_HK.UTF-8', 'en_NZ.UTF-8');
        
        foreach ($allowed_locales as $item) {
            
            
            
        }
        
        
        
        return true;
        
    }
}
