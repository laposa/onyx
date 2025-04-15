<?php
/**
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/content/default.php');

class Onyx_Controller_Node_Content_Filter extends Onyx_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        
        $node_data = $Node->nodeDetail($this->GET['id']);
        
        if ($node_data['component']['template'] == '') $node_data['component']['template'] = 'menu_UL';
        
        $Onyx_Request = new Onyx_Request("component/menu&type=taxonomy&level=2&display_all=1&id={$node_data['component']['node_id']}&template={$node_data['component']['template']}");
        
        $this->tpl->assign("FILTER", $Onyx_Request->getContent());
        $this->tpl->assign("NODE", $node_data);
        
        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }
}
