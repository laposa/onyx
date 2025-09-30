<?php
/** 
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component.php');

class Onyx_Controller_Bo_Component_Visibility extends Onyx_Controller_Bo_Component {

    /**
     * main action
     */
     
    public function mainAction() {

        parent::assignNodeData();

        //publish
        if ($this->node_data['publish'] == 1) {
            $this->node_data['publish_check'] = 'checked="checked"';
        } else {
            $this->node_data['publish_check'] = '';
        }
        
        //display in menu
        $this->node_data["display_in_menu_select_" . $this->node_data['display_in_menu']] = "selected='selected'";

        $this->tpl->assign('PUBLISHED', $this->node_data['publish'] == 1 ? 'Yes' : 'No');

        parent::parseTemplate();

        return true;
    }
}   

