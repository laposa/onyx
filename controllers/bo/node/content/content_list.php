<?php
/**
 * Copyright (c) 2010-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Content_List extends Onxshop_Controller_Bo_Node_Content_Default {
    
    /**
     * post action
     */
     
    function post() {
        
        parent::post();
        
        /**
         * container selected
         */
         
        $this->tpl->assign("SELECTED_container_{$this->node_data['component']['container']}", "selected='selected'");
        
    }
}

