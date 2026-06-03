<?php
/**
 * Copyright (c) 2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Page_Header extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        
        if (!is_numeric($this->GET['node_id'])) {
            msg('node_child: id is not numeric', 'error');
            return false;
        }
        
        $Node = new common_node();
        
        $node_detail = $Node->getDetail($this->GET['node_id']);
        $templates_info = getTemplatesInfo();

        if ($templates_info[$node_detail['node_group']][$node_detail['node_controller']]['title']) $label = $templates_info[$node_detail['node_group']][$node_detail['node_controller']]['title'];
        else $label = ucwords(str_replace(['-', '_'], ' ', $node_detail['node_controller']));
        
        if (!is_array($node_detail)) {
            msg("node_child: Node not found", 'error');
            return false;
        }

        $this->tpl->assign("LABEL", $label);
        $this->tpl->assign("NODE", $node_detail);
        
        return true;
    }
}
