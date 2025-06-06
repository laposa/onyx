<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Bo_Navigation extends Onyx_Controller {

    public $Node;

    /**
     * main action
     */

    // TODO:
    // - can we achieve smooth animations and transitions while using this?
    // - getTree vs getNodesByParent ?
    // remove priority if we dont need sortable drag and drop

     
    public function mainAction() {

        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        
        $node_id = is_numeric($this->GET['id'] ?? null) 
            ? $this->GET['id'] 
            : $_SESSION['active_pages'][0] ?? null;

        $tree = [];
        
        $parents = $this->Node->getFullPath($node_id) ? $this->Node->getFullPath($node_id) : [];

        //fill in root
        $parents[count($parents)] = 0;
        $parents = array_reverse($parents);

        foreach($parents as $index => $level) {

            $items = $this->Node->getNavigationChildren($level);
            $this->tpl->assign('ROOT', $level);

            foreach($items as $key => $item) {
                $this->tpl->assign('ITEM', $item);
                $this->tpl->assign('POSITION', $key + 1);

                $sub_items = count($this->Node->getChildren($item['id']) ?? []);
                $this->tpl->assign('HAS_CHILDREN', $sub_items > 0 ? 'has-children' : '');                   
                $this->tpl->assign('ACTIVE', in_array($item['id'], $parents) ? 'active' : '');

                $this->tpl->parse('content.level.item');
            }

            if(count($parents) == $index + 1) {
                $this->tpl->parse('content.level.action_buttons');
            }

            $this->tpl->parse('content.level');
        }

        if($node_id && $_GET['init'] === 'true') {
            header('HX-Trigger: {"loadDetail":{"nodeId" :"'. $node_id.'"}}');
        }
        
        return true;        
    }
}
