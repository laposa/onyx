<?php
/** 
 * Copyright (c) 2005-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Html extends Onxshop_Controller_Bo_Node_Content_Default {

    /**
     * post action
     */

    function post() {
    
        parent::post();
        
        $this->node_data['body_attributes'] = htmlspecialchars($this->node_data['body_attributes'], ENT_QUOTES, 'UTF-8');
        
        if ($this->node_data['content'] == '' && !$_POST['save']) $this->tpl->parse('content.empty');
    }
}

