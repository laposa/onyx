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

    public function mainAction() {

        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        
        $node_id = is_numeric($this->GET['id'] ?? null) 
            ? $this->GET['id'] 
            : $_SESSION['active_pages'][0] ?? null;

        $parents = $this->Node->getFullPath($node_id) ? $this->Node->getFullPath($node_id) : [];

        //fill in root
        $parents[count($parents)] = 0;
        $parents = array_reverse($parents);

        $this->tpl->assign('NODE_ID', $node_id);	

        foreach($parents as $index => $level) {
            $this->tpl->assign('ROOT', $level);
            $this->tpl->assign('LEVEL', $index);
            $this->tpl->assign('ACTIVE_PAGE', $node_id);
            $this->tpl->parse('content.level');
        }

        $this->tpl->assign('LAST', count($parents));
        
        return true;        
    }
}
