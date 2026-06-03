<?php
/** 
 * Copyright (c) 2025-2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_node.php');

class Onyx_Controller_Bo_Component_Component_Occurences extends Onyx_Controller {

    public $Node;
    
    /**
     * main action
     */
     
    public function mainAction()
    {
        $this->Node = new common_node();

        $nodes = $this->Node->getNodesByController($this->GET['node_controller']);

        $templates_info = getTemplatesInfo();

        if (is_array($nodes) && count($nodes) > 0) {

            foreach ($nodes as $item) {
                if ($item['publish'] == 0)  $item['class'] = 'disabled';
                $this->tpl->assign("ITEM", $item);
                $this->tpl->parse('content.list.item');
            }
            $this->tpl->parse('content.list');

        } else {
            $this->tpl->parse('content.empty');
        }

        return true;
    }

}
