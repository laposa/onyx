<?php
/**
 * Copyright (c) 2013-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Page_List extends Onxshop_Controller_Bo_Node_Content_Default {

    /**
     * post action
     */
     
    function post() {
        
        parent::post();
        
        //template
        $this->tpl->assign("SELECTED_template_{$this->node_data['component']['template']}", "selected='selected'");
        
    }
}
