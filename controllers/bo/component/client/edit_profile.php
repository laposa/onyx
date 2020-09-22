<?php
/** 
 * Copyright (c) 2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/client/edit.php');

class Onyx_Controller_Bo_Component_Client_Edit_Profile extends Onyx_Controller_Bo_Component_Client_Edit {

    /**
     * main action
     */
     
    public function mainAction() {
        
        $this->Customer = new client_customer();
        
        $this->auth = Onyx_Bo_Authentication::getInstance();
        
        $customer_id = $this->auth->getUserId();    
        if (!is_numeric($customer_id)) {
                return false;
        }
                
        if ($_POST['save']) {
        
            $_POST['client']['customer']['id'] = $customer_id;
            
            // do not allow to set certain properties           
            unset($_POST['client']['customer']['status']);
            unset($_POST['client']['customer']['group_id']);
            unset($_POST['client']['customer']['group_ids']);
            unset($_POST['client']['customer']['role_ids']);
            unset($_POST['client']['customer']['account_type']);
            unset($_POST['client']['customer']['other_data']);
            
            /**
             * update profile
             */
             
            if ($this->Customer->updateClient($_POST['client'])) {
            
                // update password 
                $this->updatePassword($customer_id);
            
                msg("Backoffice profile successfully updated");
            } else {
                msg("Can't update backoffice profile", 'error');
            }
            
        }
        
        $client_data = $this->Customer->getClientData($customer_id);
        
        $this->tpl->assign('CLIENT', $client_data);

        /**
         * only users stored in client_customer table can update their profile
         */
                
        if ($customer_id === 0) {
            $this->tpl->parse('content.other_auth');
        } else {
            $this->tpl->parse('content.form');
        }

        return true;
    }
}
