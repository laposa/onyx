<?php
/**
 * Copyright (c) 2007-2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Bin_Node_List extends Onyx_Controller {

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
        
        if (isset($this->GET['node_group']) && $this->GET['node_group'] != '') $this->tpl->assign('NODE_GROUP', $this->GET['node_group']);
        else $this->tpl->assign('NODE_GROUP', $node_detail['node_group']);
        
        $this->tpl->assign("NODE", $node_detail);
        
        //get children
        $children = $Node->getChildren($node_detail['id'], 'modified DESC');

        // Sort by removed by - keep it or not ? too much resource consuming ?
        foreach ($children as &$child) {
            $child['other_data'] = unserialize($child['other_data'] ?? '');
        }
        unset($child);

        usort($children, function($a, $b) {
            if(!isset($a['other_data']['removed']) && !isset($b['other_data']['removed'])) {
                return 0;
            }
            return $a['other_data']['removed'] <= $b['other_data']['removed'];
        });

        if (is_array($children) && count($children) > 0) {

            if (count($children) > 1000) {
                $children = array_slice($children, 0, 1000);
                msg('Bin content list detected more than 1000 items. Showing only first 1000 items.');
            }

            foreach ($children as $child) {
                if ($child['publish'] == 0)  $child['class'] = 'disabled';
                $this->tpl->assign("CHILD", $child);
                $this->tpl->assign("REMOVED", isset($child['other_data']['removed']) ? date('d. m. Y H:i:s', strtotime($child['other_data']['removed'])) : '');
                $this->tpl->parse('content.children.item');
            }
            $this->tpl->parse('content.children');
        } else {
            $this->tpl->parse('content.empty');
        }

        return true;
    }
}
