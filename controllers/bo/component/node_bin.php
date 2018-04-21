<?php
/** 
 * this component isn't used anywhere yet and standard node_delete is moving to bin by default
 *
 * Copyright (c) 2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */


class Onxshop_Controller_Bo_Component_Node_Bin extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * check input variables
         */
         
        if (is_numeric($_POST['node_id'])) {
                
                $node_id = $_POST['node_id'];
                
        } else {
            
            msg("node_bin: node_id is not numeric", 'error');
            return false;
            
        }
        
        /**
         * initialise Node object
         */
         
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        /**
         * the request seems to be valid, try to bin the node
         */
                
        if ($Node->moveToBin($node_id)) {
            
            msg("Node (id=$node_id) moved to Bin", 'ok');
        
        } else {
            
            msg("Cannot move Node (id=$node_id) to Bin", 'error');
        
        }
        
        return true;
    }
}

