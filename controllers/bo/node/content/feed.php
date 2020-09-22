<?php
/**
 * Copyright (c) 2005-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Feed extends Onxshop_Controller_Bo_Node_Content_Default {

    /**
     * pre action
     */
    
    function pre() {
    
        parent::pre();
        
        if ($_POST['node']['component']['channel_title'] == 'on') $_POST['node']['component']['channel_title'] = 1;
        else $_POST['node']['component']['channel_title'] = 0;
        
        if ($_POST['node']['component']['image'] == 'on') $_POST['node']['component']['image'] = 1;
        else $_POST['node']['component']['image'] = 0;

        if ($_POST['node']['component']['description'] == 'on') $_POST['node']['component']['description'] = 1;
        else $_POST['node']['component']['description'] = 0;
        
        if ($_POST['node']['component']['content'] == 'on') $_POST['node']['component']['content'] = 1;
        else $_POST['node']['component']['content'] = 0;

        if ($_POST['node']['component']['pubdate'] == 'on') $_POST['node']['component']['pubdate'] = 1;
        else $_POST['node']['component']['pubdate'] = 0;

        if ($_POST['node']['component']['copyright'] == 'on') $_POST['node']['component']['copyright'] = 1;
        else $_POST['node']['component']['copyright'] = 0;
        
        if ($_POST['node']['component']['ajax'] == 'on') $_POST['node']['component']['ajax'] = 1;
        else $_POST['node']['component']['ajax'] = 0;
    }

    /**
     * post action
     */

    function post() {
    
        parent::post();
    
        $this->node_data['component']['channel_title'] = ($this->node_data['component']['channel_title']) ? 'checked="checked"' : '';
        $this->node_data['component']['image'] = ($this->node_data['component']['image']) ? 'checked="checked"' : '';
        $this->node_data['component']['description'] = ($this->node_data['component']['description']) ? 'checked="checked"' : '';
        $this->node_data['component']['content'] = ($this->node_data['component']['content']) ? 'checked="checked"' : '';
        $this->node_data['component']['pubdate'] = ($this->node_data['component']['pubdate']) ? 'checked="checked"' : '';
        $this->node_data['component']['copyright'] = ($this->node_data['component']['copyright']) ? 'checked="checked"' : '';
        $this->node_data['component']['ajax'] = ($this->node_data['component']['ajax']) ? 'checked="checked"' : '';
    }
}
