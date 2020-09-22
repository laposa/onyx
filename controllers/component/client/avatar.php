<?php
/**
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Client_Avatar extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/client/client_customer.php');
        $Customer = new client_customer();
        
        /**
         * Display
         */
         
        if (is_numeric($this->GET['customer_id'])) {
            $customer_detail = $Customer->getDetail($this->GET['customer_id']);
        }
         
        $this->tpl->assign('CUSTOMER', $customer_detail);
        

        return true;
    }
}

