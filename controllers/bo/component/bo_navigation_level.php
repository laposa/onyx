<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Bo_Navigation_Level extends Onyx_Controller {

    public $Node;

    /**
     * main action
     */
     
    public function mainAction() {

        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        
        $node_id = is_numeric($this->GET['id'] ?? null) 
            ? $this->GET['id'] 
            : $_SESSION['active_pages'][0] ?? null;

        $active_page = $this->GET['active'] ?? 0;
        $active_path = $this->Node->getFullPath($active_page) ?? [];
        
        //fill in root and reverse
        $active_path[count($active_path)] = 0;
        $active_path = array_reverse($active_path);

        $level = array_search($node_id, $active_path) ?? -1;
        
        $children = $this->Node->getNavigationChildren($node_id);
        $this->tpl->assign('ROOT', $node_id);

        foreach($children as $key => $item) {

            $this->tpl->assign('ITEM', $item);
            $this->tpl->assign('POSITION', $key + 1);
            $this->tpl->assign('TARGET', $level + 1);
            
            $sub_items = count($this->Node->getChildren($item['id']) ?? []);
            $this->tpl->assign('HAS_CHILDREN', $sub_items > 0 ? 'has-children' : '');
            $this->tpl->assign('ACTIVE', in_array($item['id'], $active_path) ? 'active' : '');

            $this->tpl->parse('content.item');
        }

        if(count($children) == 0) {
            $this->tpl->parse('content.action_buttons');
        }

        //initialization
        if($level == count($active_path) - 1) {
            header('HX-Trigger: {"loadDetail":{"nodeId" :"'.$node_id.'"}}');
        }

        return true;        
    }
}
