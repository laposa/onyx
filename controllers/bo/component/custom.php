<?php
/** 
 * Copyright (c) 2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/x.php');

class Onyx_Controller_Bo_Component_Custom extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */
     
    public function mainAction() {

        // get details
        $node = new common_node();
        $node_data = $node->nodeDetail($this->GET['node_id'] ?? $_POST['node']['id']);

        // save
        if (isset($_POST['save'])) {

            $save_data = $_POST['node'];
            $save_data['title'] = $node_data['title'];
            $node->nodeUpdate($save_data);
            return true;
            
        }

        // pick component template by node_controller
        $template = "bo/component/custom/{$node_data['node_controller']}.html";
        if (!preg_match('/^[a-z0-9_-]+$/', $node_data['node_controller']) || getTemplateDir($template) == '') return false;
        $this->_template_dir = getTemplateDir($template);
        $this->_initTemplate($template);

        // parent node
        $this->tpl->assign('NODE', $node_data);

        parent::parseTemplate();

        return true;
    }
}   

