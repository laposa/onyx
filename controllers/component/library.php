<?php
/** 
 * Copyright (c) 2020 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_node.php');

class Onyx_Controller_Component_Library extends Onyx_Controller {
    
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
        
        if (is_numeric($this->GET['source_node_id'])) $source_node_id = (int) $this->GET['source_node_id'];
        else $source_node_id = false;

        if (is_numeric($this->GET['add_to_node_id'])) $add_to_node_id = (int) $this->GET['add_to_node_id'];
        else $add_to_node_id = false;

        if (is_numeric($this->GET['add_to_container'])) $add_to_container = $this->GET['add_to_container'];
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

        foreach ($used_content_types as $item) {
            $item['example'] = new Onyx_Request("node~id={$item['id']}~");
            
            $this->tpl->assign('ITEM', $item);
            if ($add_to_node_id) $this->tpl->parse('content.item.action');
            if (!preg_match("/_item$/", $item['node_controller'])) {
                // breakdown
                $this->tpl->parse('content.breakdown_item');
                // full item
                $this->tpl->parse('content.item');
            }
        }
    }
}
