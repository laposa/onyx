<?php
/** 
 * Copyright (c) 2013-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'models/client/client_action.php';

class Onxshop_Controller_Component_Client_Action_Add extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        $data = array();
        $data['customer_id'] = (int) $_SESSION['client']['customer']['id']; // mandatory
        $data['node_id'] = $this->GET['node_id']; // mandatory
        if (!empty($this->GET['action_id'])) $data['action_id'] = $this->GET['action_id'];
        if (!empty($this->GET['network'])) $data['network'] = $this->GET['network'];
        $data['action_name'] = $this->GET['action_name']; // mandatory
        if (!empty($this->GET['object_name'])) $data['object_name'] = $this->GET['object_name'];
        
        $ClientAction = new client_action();
        
        if ($ClientAction->insertAction($data)) {
        
            msg("Action inserted to the database", "ok", 1);
            
        } else {
            
            msg("Unable to insert action to database, missing parameters " . print_r($data, true), "error", 1);
        
        }
        
        return true;
    }

}
