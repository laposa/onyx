<?php
/**
 * Copyright (c) 2006-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Menu extends Onxshop_Controller_Bo_Node_Content_Default {

    /**
     * pre action
     */

    function pre() {
    
        parent::pre();
        
        if ($_POST['node']['component']['display_title'] == 'on') $_POST['node']['component']['display_title'] = 1;
        else $_POST['node']['component']['display_title'] = 0;
        if ($_POST['node']['component']['display_strapline'] == 'on') $_POST['node']['component']['display_strapline'] = 1;
        else $_POST['node']['component']['display_strapline'] = 0;
        if ($_POST['node']['component']['display_all'] == 'on') $_POST['node']['component']['display_all'] = 1;
        else $_POST['node']['component']['display_all'] = 0;
    }
    
    /**
     * post action
     */

    function post() {
    
        parent::post();
        
        $this->node_data['component']['display_title']        = ($this->node_data['component']['display_title']) ? 'checked="checked"'      : '';
        $this->node_data['component']['display_strapline']        = ($this->node_data['component']['display_strapline']) ? 'checked="checked"'      : '';
        $this->node_data['component']['display_all']        = ($this->node_data['component']['display_all']) ? 'checked="checked"'      : '';
        $this->tpl->assign("SELECTED_{$this->node_data['component']['template']}", "selected='selected'");
    }
}
