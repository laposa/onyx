<?php
/** 
 * Copyright (c) 2020-2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_node.php');

class Onyx_Controller_Component_Library extends Onyx_Controller {

    public $Node;
    
    /**
     * main action
     */
     
    public function mainAction()
    {

        // hack to pass _SESSION.fe_edit_mode even before it's called again from fe_edit
        // consider moving this to $Bootstrap->initPreAction
        if (Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {
            $_Onyx_Request = new Onyx_Request('bo/component/fe_edit_mode~fe_edit_mode=preview~');
        }

        // get input variables
        
        if (is_numeric($this->GET['source_node_id'] ?? null)) $source_node_id = (int) $this->GET['source_node_id'];
        else $source_node_id = false;

        if (is_numeric($this->GET['add_to_node_id'] ?? null)) $add_to_node_id = (int) $this->GET['add_to_node_id'];
        else $add_to_node_id = false;

        if (is_numeric($this->GET['add_to_container'] ?? null)) $add_to_container = $this->GET['add_to_container'];
        else $add_to_container = 0;

        // initialise object
        $this->Node = new common_node();
        
        // process adding

        if ($source_node_id && $add_to_node_id) {
            $this->addItem($source_node_id, $add_to_node_id, $add_to_container);
        } else {
            // list all used items
            $this->listItems($add_to_node_id);
        }

        // show cancel button only when editing
        if (Onyx_Bo_Authentication::getInstance()->isAuthenticated()) $this->tpl->parse('content.cancel');

        return true;
    }
    
    public function addItem($source_node_id, $add_to_node_id, $add_to_container)
    {
        msg("Adding $source_node_id to node $add_to_node_id container $add_to_container");
        $new_node_id = $this->Node->duplicateNode($source_node_id, $add_to_node_id, 0, $add_to_container);
        if (is_numeric($new_node_id)) OnyxGoTo("page/$add_to_node_id?fe_edit_mode=edit#onyx-fe-edit-node-id-$new_node_id");
    }

    public function listItems($add_to_node_id)
    {
        $used_content_types = $this->Node->getUsedContentTypes();

        // filter
        require_once('controllers/bo/component/node_type_menu.php');
        $Node_Type_Menu = new Onyx_Controller_Bo_Component_Node_Type_Menu();
        $templates_info = $Node_Type_Menu->retrieveTemplateInfo();

        $list = [];
        foreach ($used_content_types as $item) {

            // show only content types with visibility attribute set to true, or not set at all
            if (
                $templates_info['content'][$item['node_controller']]['visibility'] == true || 
                $templates_info['content'][$item['node_controller']]['visibility'] === NULL 
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
            
            $item['example'] = new Onyx_Request("node~id={$item['id']}~");
            
            $this->tpl->assign('ITEM', $item);
            if ($add_to_node_id) $this->tpl->parse('content.item.action');
            // breakdown
            $this->tpl->parse('content.breakdown_item');
            // full item
            $this->tpl->parse('content.item');
        }
    }
}
