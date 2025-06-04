<?php
/** 
 * Copyright (c) 2005-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Component_Client_Edit extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        if (empty($_SESSION['client']['customer']['id']) && !Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {
            msg('client_edit: You must be logged in first.', 'error');
            onyxGoTo("/");
        }
        
        require_once('models/client/client_customer.php');
        $Customer = new client_customer();
        $Customer->setCacheable(false);
        
        $customer_id = $_SESSION['client']['customer']['id'];   
        if (!is_numeric($customer_id)) return false;
        
        if ($_POST['save'] ?? false) {
        
            $_POST['client']['customer']['id'] = $customer_id;

            // do not allow to set certain properties           
            unset($_POST['client']['customer']['status']);
            unset($_POST['client']['customer']['group_id']);
            unset($_POST['client']['customer']['group_ids']);
            unset($_POST['client']['customer']['role_ids']);
            unset($_POST['client']['customer']['account_type']);
            unset($_POST['client']['customer']['other_data']);

            /**
             * check birthday field format
             */
             
            if ($_POST['client']['customer']['birthday']) {
                
                // check, expected as dd/mm/yyyy
                if (!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $_POST['client']['customer']['birthday'])) {
                    msg('Invalid format for birthday, use dd/mm/yyyy', 'error');
                    return false;
                }
                
                // Format to ISO
                $_POST['client']['customer']['birthday'] = strftime('%Y-%m-%d', strtotime(str_replace('/', '-', $_POST['client']['customer']['birthday'])));
            }
            
            /**
             * update
             */
             
            if ($Customer->updateClient($_POST['client'])) {
                msg(I18N_CUSTOMER_DATA_UPDATED);
            } else {
                msg("Can't update client data", 'error');
            }
           
            onyxGoTo("/page/{$this->page_id}");
        }
        
        $client_data = $Customer->getClientData($customer_id);
        
        $client_data['customer']['newsletter'] = ($client_data['customer']['newsletter'] == 1) ? 'checked="checked" ' : '';
        
        // format birthday only if available to avoid 01/01/1970 by default
        if ($client_data['customer']['birthday'] != '') $client_data['customer']['birthday'] = strftime('%d/%m/%Y', strtotime($client_data['customer']['birthday']));
        
        $this->tpl->assign('CLIENT', $client_data);
        
        /**
         * show password field only if previously set
         */
        
        if ($client_data['customer']['password']) $this->tpl->parse('content.password');
        
        return true;
    }
}
