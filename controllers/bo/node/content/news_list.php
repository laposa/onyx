<?php
/**
 * Copyright (c) 2006-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_News_List extends Onxshop_Controller_Bo_Node_Content_Default {

    /**
     * pre action
     */

    function pre() {
    
        parent::pre();
        
        if (!(is_numeric($_POST['node']['component']['limit']) && $_POST['node']['component']['limit'] > 0)) $_POST['node']['component']['limit'] = 5;
        if ($_POST['node']['component']['pagination'] == 'on') $_POST['node']['component']['pagination'] = 1;
        else $_POST['node']['component']['pagination'] = 0;
        if ($_POST['node']['component']['display_title'] == 'on') $_POST['node']['component']['display_title'] = 1;
        else $_POST['node']['component']['display_title'] = 0;

    }
    
    /**
     * post action
     */
     
    function post() {
    
        parent::post();
        
        /**
         * other options
         */
    
        $this->node_data['component']['pagination']        = ($this->node_data['component']['pagination']) ? 'checked="checked"'      : '';
        $this->node_data['component']['display_title']        = ($this->node_data['component']['display_title']) ? 'checked="checked"'      : '';
        
        /**
         * template selected
         */
         
        $this->tpl->assign("SELECTED_template_{$this->node_data['component']['template']}", "selected='selected'");
        
    }
}

