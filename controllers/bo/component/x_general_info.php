<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/x.php');
require_once('models/common/common_node.php');
class Onyx_Controller_Bo_Component_X_General_Info extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */
     
    public function mainAction() {

        // get details
        $this->Node = new common_node();
        $this->node_data = $this->Node->nodeDetail($this->GET['node_id']);
        if ($this->node_data) $this->tpl->assign('NODE', $this->node_data);

        // display title
        if (!is_numeric($this->node_data['display_title'])) $this->node_data['display_title'] = $GLOBALS['onyx_conf']['global']['display_title'];

        if ($this->node_data['display_title'] == 1) {
            $this->node_data['display_title_check'] = 'checked="checked"';
        } else {
            $this->node_data['display_title_check'] = '';
        }

        parent::parseTemplate();

        return true;
    }

}   

