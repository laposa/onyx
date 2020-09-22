<?php
/**
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Component extends Onxshop_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {

        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        
        $node_data = $Node->nodeDetail($this->GET['id']);
        
        $node_data['component']['template'] = str_replace('.html', '', $node_data['component']['template']);
        $node_data['component']['controller'] = str_replace('.php', '', $node_data['component']['controller']);
        
        if ($node_data['component']['controller'] == "") {
            $request = "component/{$node_data['component']['template']}";
        } else {
            $request = "component/{$node_data['component']['controller']}@component/{$node_data['component']['template']}";
        }

        $_Onxshop_Request = new Onxshop_Request("$request&amp;node_id={$node_data['id']}&amp;fe_edit=0&amp;{$node_data['component']['parameter']}");
        $node_data['content'] = $_Onxshop_Request->getContent();
        
        $this->tpl->assign("NODE", $node_data);
        
        $this->displayTitle($node_data);

        return true;
    }
}
