<?php
/**
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Component_Client_Customer_Detail extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        /**
         * check input
         */
         
        if (empty($_SESSION['client']['customer']['id']) && !Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {
            msg('controllers/client/customer_detail: You must logged in.', 'error');
            onyxGoTo("/");
        } else {
            if (is_numeric($this->GET['customer_id']) && constant('ONYX_IN_BACKOFFICE')) $customer_id = $this->GET['customer_id'];
            else $customer_id = $_SESSION['client']['customer']['id'];  
        }
        
        if (!is_numeric($customer_id)) return false;
        
        /**
         * initialize
         */
         
        require_once('models/client/client_customer.php');
        
        $Customer = new client_customer();
        $Customer->setCacheable(false);
        
        /**
         * get customer detail
         */
        
        $customer_detail = $Customer->getDetail($customer_id);
        
        if (is_array($customer_detail)) {
            $this->tpl->assign('ITEM', $customer_detail);
        } else {
            msg('controllers/client/customer_detail: cannot get detail', 'error');
        }
        
        return true;
    }
}
