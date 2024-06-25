<?php
/**
 * Copyright (c) 2007-2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Node_List_Pages extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        
        if (!is_numeric($this->GET['id'])) {
            msg('node_child: id is not numeric', 'error');
            return false;
        }
        
        $Node = new common_node();
        
        $node_detail = $Node->getDetail($this->GET['id']);
        
        if (!is_array($node_detail)) {
            msg("node_child: Node not found", 'error');
            return false;
        }
        
        /**
         * set node group as parent if not provided
         */
        
        if ($this->GET['node_group'] != '') $this->tpl->assign('NODE_GROUP', $this->GET['node_group']);
        else $this->tpl->assign('NODE_GROUP', $node_detail['node_group']);
        
        $this->tpl->assign("NODE", $node_detail);
        
        //get children
        $children = $Node->getChildren($node_detail['id'], 'parent_container ASC, priority DESC, id ASC');
        
        if (is_array($children) && count($children) > 0) { 
            foreach ($children as $child) {
                if ($child['node_group'] == 'page') {
                    if ($child['publish'] == 0)  $child['class'] = 'disabled';
                    $this->tpl->assign("CHILD", $child);
                    $this->tpl->parse('content.children.item');
                }
            }
            $this->tpl->parse('content.children');
        } else {
            $this->tpl->parse('content.empty');
        }

        return true;
    }
}
