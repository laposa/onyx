<?php
/** 
 * Copyright (c) 2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Adaptive extends Onxshop_Controller_Bo_Node_Content_Default {

    /**
     * post action
     */

    function post() {
    
        parent::post();

        $this->tpl->assign("SELECT_condition_" . $this->node_data['component']['condition'], 'selected="selected"');        
    }
}

