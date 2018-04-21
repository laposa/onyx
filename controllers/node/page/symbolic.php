<?php
/**
 * Copyright (c) 2006-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/page/default.php');

class Onxshop_Controller_Node_Page_Symbolic extends Onxshop_Controller_Node_Page_Default {

    /**
     * main action
     */
     
    public function mainAction() {

        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        
        $node_data = $Node->nodeDetail($this->GET['id']);
        
        $this->tpl->assign("NODE", $node_data);
        
        if ($node_data['component']['href'] != '') {
            header("HTTP/1.1 301 Moved Permanently");
            
            if (preg_match('/\:\/\//', $node_data['component']['href'])) onxshopGoTo($node_data['component']['href'], 2);
            else onxshopGoTo($node_data['component']['href']);
        }

        return true;
    }
}
