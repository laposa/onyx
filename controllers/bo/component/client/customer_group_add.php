<?php
/**
 *
 * Copyright (c) 2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Client_Customer_Group_Add extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * input
         */
         
        $group_data = $_POST['customer-group'];
        
        /**
         * save action
         */
        
        if (isset($_POST['save'])) $this->saveGroup($group_data);
        
        /**
         * show form
         */
        
        $this->tpl->assign('CUSTOMER_GROUP', $group_data);
        $this->tpl->parse('content.form');
        
        return true;
    }
    
    /**
     * save group
     */
     
    public function saveGroup($group_data) {
        
        if (!is_array($group_data)) return false;
        
        require_once('models/client/client_group.php');
        $ClientGroup = new client_group();
        
        $data = array();
        $data['name'] = $group_data['name'];
        $data['description'] = '';

        /**
         * save actual group
         */

        if ($id = $ClientGroup->saveGroup($data)) {
            
            msg("Customers group saved under name {$data['name']} and ID $id");
            return true;
            
        } else {
            
            msg("Cannot save customers group", 'error');
            return false;
        }
        
    }
}
