<?php
/**
 * Copyright (c) 2010-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onyx_Controller_Node_Content_Content_List extends Onyx_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
        
        $node_id = $this->GET['id'];
        
        require_once('models/common/common_node.php');
        
        $this->Node = new common_node();
        $node_data = $this->Node->nodeDetail($node_id);
        
        
        /**
         * prepare query
         */
        
        $list_node_id = $node_data['component']['node_id'];
        $list_container = $node_data['component']['container'];
        
        /**
         * get parent page
         */
        
        $parent_node_detail = $this->Node->getDetail($list_node_id);
        if ($parent_node_detail['node_group'] == 'page') $parent_page_id = $list_node_id;
        else $parent_page_id = $this->Node->getParentPageId($list_node_id);
        $this->tpl->assign('PARENT_PAGE_ID', $parent_page_id);
        
        /**
         * generate list
         */
         
        $this->generateList($list_node_id, $list_container);
        
                
        $this->tpl->assign("NODE", $node_data);
        
        
        /**
         * display title
         */
         
        $this->displayTitle($node_data);

        return true;
    }
    
    /**
     * get list
     */
     
    public function generateList($list_node_id, $list_container, $list_node_group = false, $list_node_controller = false) {
        
        if (!is_numeric($list_node_id)) return false;
        if (!is_numeric($list_container)) return false;
        
        if ($list = $this->Node->getList("parent = {$list_node_id} AND parent_container = {$list_container}", 'priority DESC, id ASC')) {
                
            foreach ($list as $item) {
                
                if ($item['publish'] == 1) {
                    $this->tpl->assign('ITEM', $item);
                    $this->tpl->parse('content.item');
                }

            }
        } else return false;

    }
}
