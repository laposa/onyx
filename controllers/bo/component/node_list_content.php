<?php
/**
 * Copyright (c) 2007-2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
require_once('controllers/bo/component/file.php');

class Onyx_Controller_Bo_Component_Node_List_Content extends Onyx_Controller {

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
        
        if (is_array($children) && count($children) > 0 && array_search('content', array_column($children, 'node_group'))) { 
            foreach ($children as $key=>$child) {
                if ($child['publish'] == 0)  $child['class'] = 'disabled';
                if ($child['node_group'] == 'content') {
                    $content_excerpt = "";
                    if($child['content'] != "") {
                        $content = strip_tags($child['content']);
                        $content_excerpt = strlen($content > 200) ? substr($content, 0, 200) . "..." : $content;
                    }
                    $sub_items = $Node->getChildren($child['id'], 'parent_container ASC, priority DESC, id ASC');
                    
                    $_Onyx_Request = new Onyx_Request("bo/component/file_list~type=add_to_node:node_id={$child['id']}:relation=node~");
                    $this->tpl->assign('FILE_LIST', $_Onyx_Request->getContent());
                    $this->tpl->assign("CHILD", $child);
                    $this->tpl->assign("CONTENT_EXCERPT", $content_excerpt);
                    $this->tpl->assign("SUB_ITEMS", count($sub_items) ?? 0);
                    $this->tpl->assign("INDEX", $key + 1);
                    $this->tpl->parse('content.children.item');
                };
            }
            $this->tpl->parse('content.children');
        } else {
            $this->tpl->parse('content.empty');
        }

        return true;
    }
}
