<?php
/** 
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Component_Searchbox extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();
        $this->tpl->assign("NODE_CONF", $node_conf);
        
        return true;
    }
}
