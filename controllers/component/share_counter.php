<?php
/** 
 * Copyright (c) 2013-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Share_Counter extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * get input
         */
         
        $node_id = $this->GET['node_id'];
        $type = $this->GET['type'];
        
        /**
         * check value
         */
         
        if (!is_numeric($node_id)) return false;
        
        /**
         * initialize
         */
         
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        /**
         * increment value
         */
         
        $Node->incrementShareCounter($node_id);
        
        /**
         * if user is logged in save action
         */
        
        $this->saveClientAction($node_id, $type);
        
        return true;
        
    }
    
    /**
     * saveClientAction
     */
     
    public function saveClientAction($node_id, $network) {
        
        if (!is_numeric($node_id)) return false;
        
        require_once('models/client/client_action.php');
        $ClientAction = new client_action();
        
        $data = array();
        $data['customer_id'] = (int)$_SESSION['client']['customer']['id'];
        $data['node_id'] = $node_id;
        if ($network) $data['network'] = $network;
        $data['action_name'] = 'share_intention';
        
        if ($inserted_id = $ClientAction->insertAction($data)) return $inserted_id;
        else msg("Cannot save share action initiated from share_counter", 'error', 1);
        
    }
}
