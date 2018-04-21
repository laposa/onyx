<?php
/**
 *
 * Copyright (c) 2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Client_Customer_Group_Modify extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * input
         */
         
        $group_data = $_POST['customer-group'];
        
        if (is_numeric($_SESSION['bo']['customer-filter']['group_id'])) {
            $group_data['id'] = $_SESSION['bo']['customer-filter']['group_id'];
        } else {
            msg("Group ID is not numeric", 'error');
            return false;
        }
        
        /**
         * initiate
         */
         
        require_once('models/client/client_group.php');
        $this->ClientGroup = new client_group();
        
        /**
         * save action
         */
        
        if (isset($_POST['save'])) {
            $this->saveGroup($group_data);
        } else {
            $group_data = $this->ClientGroup->getDetail($group_data['id']);
        }
        
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

        /**
         * save actual group
         */

        if ($id = $this->ClientGroup->saveGroup($group_data)) {
            
            msg("Customers group saved under name {$group_data['name']} and ID $id");
            return true;
            
        } else {
            
            msg("Cannot save customers group", 'error');
            return false;
        }
        
    }
}
