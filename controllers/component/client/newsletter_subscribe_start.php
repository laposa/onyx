<?php
/**
 * First step of the newsletter subscribe process
 *
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Client_Newsletter_Subscribe_Start extends Onxshop_Controller {
        
    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * include node configuration
         */
        
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();
        $this->tpl->assign('NODE_CONF', $node_conf);
        
        return true;
    }
}
