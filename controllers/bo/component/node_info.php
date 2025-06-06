<?php
/**
 * Copyright (c) 2008-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Component_Node_Info extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        if (is_numeric($this->GET['id'])) $node_id = $this->GET['id'];
        else {
            msg('node_edit: node_id is not numeric', 'error');
            return false;
        }
    
        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        $node_data = $Node->detail($node_id);
        $content_excerpt = "";
        $sub_items = count($Node->getChildren($node_data['id']) ?? []);


        if($node_data['content'] != "") {
            $content = strip_tags($node_data['content']);
            $content_excerpt = strlen($content) > 500 ? substr($content, 0, 500) . "..." : $content;
        }

        $file_list = new Onyx_Request("bo/component/file_list~type=add_to_node:node_id={$node_data['id']}:relation=node~");
        
        $this->tpl->assign("NODE", $node_data);
        $this->tpl->assign("CONTENT_EXCERPT", $content_excerpt);
        $this->tpl->assign("SUB_ITEMS", $sub_items);
        $this->tpl->assign('FILE_LIST', $file_list->getContent());
        
        return true;
    }
}

