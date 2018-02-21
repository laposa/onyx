<?php
/**
 * Copyright (c) 2009-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/configuration.php');

class Onxshop_Controller_Bo_Component_Configuration_Ecommerce_Invoice extends Onxshop_Controller_Bo_Component_Configuration {
    
    /**
     * custom action
     */
     
    public function customAction($conf) {
        /**
        ecommerce_invoice   company_name
        ecommerce_invoice   company_logo
        ecommerce_invoice   footer
        ecommerce_invoice   return_address
        **/
        
        return $conf;
    
    }
    
}
