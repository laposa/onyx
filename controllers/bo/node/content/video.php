<?php
/**
 * Copyright (c) 2015-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Video extends Onxshop_Controller_Bo_Node_Content_Default {

    /**
     * pre action
     */

    function pre() {
    
        parent::pre();
        
        if ($_POST['node']['component']['autoplay'] == 'on') $_POST['node']['component']['autoplay'] = 1;
        else $_POST['node']['component']['autoplay'] = 0;
        
    }
    
    /**
     * post action
     */
     
    function post() {
    
        parent::post();
        
        /**
         * other options
         */
         
        $this->node_data['component']['autoplay'] = ($this->node_data['component']['autoplay']) ? 'checked="checked"' : '';
        
        $this->tpl->assign("SELECTED_provider_{$this->node_data['component']['provider']}", 'selected');
    }
}

