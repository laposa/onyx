<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_node.php');

class Onyx_Controller_Bo_Component_Component_List extends Onyx_Controller {

    public $Node;
    
    /**
     * main action
     */
     
    public function mainAction()
    {
        // initialise object
        $this->Node = new common_node();
        
        // list all used items
        $this->listItems();

        return true;
    }

    public function listItems()
    {
        $used_content_types = $this->Node->getUsedContentTypes();

        // filter
        require_once('controllers/bo/component/node_type_menu.php');
        $Node_Type_Menu = new Onyx_Controller_Bo_Component_Node_Type_Menu();
        $templates_info = $Node_Type_Menu->retrieveTemplateInfo();

        $list = [];
        foreach ($used_content_types as $item) {

            $item['count'] = count($this->Node->getNodesByController($item['node_controller']) ?? 0);

            // show only content types with visibility attribute set to true, or not set at all
            if (
                $templates_info[$item['node_group']][$item['node_controller']]['visibility'] == true || 
                $templates_info[$item['node_group']][$item['node_controller']]['visibility'] === NULL 
            ) {
                
                $list_item = $item;
                
                // add content info
                if (array_key_exists($item['node_controller'], $templates_info['content'])) $list_item['info'] = $templates_info['content'][$item['node_controller']];
                else $list_item['info'] = array('title'=>$item['node_controller']);
                
                // don't show any *-item content types
                if (!preg_match('/[-_]item$/', $item['node_controller'])) array_push($list, $list_item);
            }
        }

        foreach ($list as $item) {
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse('content.item');
        }
    }
}
