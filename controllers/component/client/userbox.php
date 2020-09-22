<?php
/**
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Client_Userbox extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/client/client_customer.php');
        $Customer = new client_customer();
        
        /**
         * include node configuration
         */
        
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();
        $this->tpl->assign('NODE_CONF', $node_conf);
        
        /**
         * Display
         */
         
        if ($_SESSION['client']['customer']['id'] > 0 && is_numeric($_SESSION['client']['customer']['id'])) {
            $customer_detail = $Customer->getDetail($_SESSION['client']['customer']['id']);
            $this->tpl->assign('CUSTOMER', $customer_detail);
            $this->tpl->parse('content.customer');
        } else {
            $this->tpl->parse('content.register');
            $this->tpl->parse('content.login');
        }

        return true;
    }
}

