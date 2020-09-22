<?php
/**
 * Copyright (c) 2010-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Component_Client_Customer_Edit extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        /**
         * check input
         */
         
        if ($_SESSION['client']['customer']['id'] == 0 && !Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {
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
        
        $this->Customer = new client_customer();
        $this->Customer->setCacheable(false);
        
        /**
         * save
         */
         
        if (is_array($_POST['client']['customer'])) {
        
            /**
             * input data
             */
             
            $data_to_save = $_POST['client']['customer'];
            $data_to_save['id'] = $customer_id;
            
            /**
             * check birthday field format
             */
             
            if ($data_to_save['birthday']) {
                
                // check, expected as dd/mm/yyyy
                if (!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $data_to_save['birthday'])) {
                    msg('Invalid format for birthday, use dd/mm/yyyy', 'error');
                    return false;
                }
                
                // Format to ISO
                $data_to_save['birthday'] = strftime('%Y-%m-%d', strtotime(str_replace('/', '-', $data_to_save['birthday'])));
            }
            
            /**
             * save
             */
            
            $this->saveDetail($data_to_save);
        }
            
        /**
         * get customer detail
         */
        
        $customer_detail = $this->Customer->getDetail($customer_id);
        
        if (is_array($customer_detail)) {
            $this->tpl->assign('ITEM', $customer_detail);
        } else {
            msg('controllers/client/customer_edit: cannot get detail', 'error');
        }
        
        return true;
    }
    
    /**
     * save
     */
     
    public function saveDetail($data) {
        
        if ($this->Customer->updateCustomer($data)) msg('saved');
        else msg('failed', 'error');
        
    }
}
