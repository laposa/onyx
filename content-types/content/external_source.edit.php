<?php
/**
 * Copyright (c) 2007-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onyx_Controller_Bo_Node_Content_External_source extends Onyx_Controller_Bo_Node_Content_Default {

    /**
     * pre action
     */

    function pre() {
        
        parent::pre();
        
        if ($_POST['component']['image'] == 'on') $_POST['component']['image'] = 1;
        else $_POST['component']['image'] = 0;
    }

    /**
     * post action
     */

    function post() {
    
        parent::post();
        
        $this->node_data['component']['image']        = ($this->node_data['component']['image']) ? 'checked="checked"'      : '';
    }
}

