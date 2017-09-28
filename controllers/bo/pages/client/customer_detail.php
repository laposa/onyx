<?php
/**
 * Customer detail controller
 *
 * Copyright (c) 2005-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('models/client/client_customer.php');

class Onxshop_Controller_Bo_Pages_Client_Customer_Detail extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {

        $this->Customer = new client_customer();

        if (is_numeric($this->GET['id'])) $customer_id = $this->GET['id'];
        else $customer_id = 0;
        
        $client_data = array();
        $client_data = $this->Customer->getClientData($customer_id);
        
        $this->tpl->assign('CLIENT', $client_data);

        return true;
    }
}
