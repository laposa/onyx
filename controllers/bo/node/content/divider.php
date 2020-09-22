<?php
/**
 * Copyright (c) 2006-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onyx_Controller_Bo_Node_Content_Divider extends Onyx_Controller_Bo_Node_Content_Default {

    /**
     * post action
     */

    function post() {
        
        parent::post();
        
        $this->tpl->assign("SELECTED_{$this->component_data['type']}", "selected='selected'");
    }
}
