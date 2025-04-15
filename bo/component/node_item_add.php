<?php
/** 
 * Copyright (c) 2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Node_Item_Add extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        $Node = new common_node();
    
        if (is_numeric($this->GET['parent'])) $parent = $this->GET['parent'];
        else {
            msg('(numeric) parent parameter is requred');
            return false;
        }
        $parent_node = $Node->getDetail($this->GET['parent']);
        $this->tpl->assign("PARENT_NODE", $parent_node);

        $node_data = $_POST['node'];
        
        if ($_POST['save'] ?? false) {
        
            /**
             * pre-populate content
             */
            
            $node_data['content'] = '
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
        <ul>
        <li>velit esse cillum dolore</li>
        <li>consectetur adipisicing elit</li>
        <li>occaecat cupidatat non proident</li>
        </ul>
        <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            ';
            
            /**
             * insert a new node
             */
            
            if($id = $Node->nodeInsert($node_data)) {
            
                msg(ucfirst($node_data['node_group']) ." has been added.");
                
                //quick pages builder
                //$Page_builder = new Onyx_Request("bo/page_builder@blank&parent=$id&node_group={$node_data['node_group']}&node_controller={$node_data['node_controller']}");
            }
        }
        
        $this->tpl->assign('NODE', $node_data);

        return true;
    }
}
