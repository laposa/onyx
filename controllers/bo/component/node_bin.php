<?php
/** 
 *
 * Copyright (c) 2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */


class Onyx_Controller_Bo_Component_Node_Bin extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        /**
         * initialise Node object
         */
         
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        /**
         * check input variables
         */
         
        if (is_numeric($this->GET['id'])) $delete_id = $this->GET['id'];
        else return false;

        $node_data = $Node->detail($delete_id);
        
        //move to bin
        if ($this->GET['trash'] && is_numeric($delete_id)) {
        
            $id_map = $Node->getIdMap();
            
            /**
             * create confirmation code
             */
            
            $confirmation_code = md5($delete_id . session_id()); 
            $this->tpl->assign('CONFIRMATION_CODE', $confirmation_code);
            $this->tpl->assign('NODE_GROUP', $node_data['node_group']);
            $this->tpl->assign('PARENT', $node_data['parent']);
            
            /**
             * safety check we are not trying to delete some core page
             */
             
            if (!array_search($delete_id, $id_map)) {
            
    
                if (!is_array($node_data)) {
                    msg("Content ID {$delete_id} does not exists", 'error');
                    return false;
                }
                
                
                if ($this->GET['confirm']) {
                    
                    //delete only if confirmation code match
                    if ($this->GET['confirm'] === $confirmation_code) {
                        
                        if ($Node->moveToBin($delete_id)) {
                        
                            msg("{$node_data['node_group']} \"{$node_data['title']}\" (id={$node_data['id']}) has been moved to bin");
                        
                            //if it was a "page", than go to parent page
                            if ($this->GET['ajax'] == 0) {
                                if ($node_data['node_group'] == 'page') onyxGoTo("/page/{$node_data['parent']}");
                                else onyxGoTo($_SESSION['last_diff'], 2);
                            }
                        
                        } else {
                            
                            msg("Can't move to bin!", 'error');
                        
                        }
                        
                    } else {
                        
                        msg("node_bin: incorrect confirmation code", 'error');
                    
                    }
                                        
                } else {
                
                    //get children
                    $children = $Node->listing("parent = {$delete_id}");
                    
                    foreach ($children as $child) {
                        $this->tpl->assign("CHILD", $child);
                        $this->tpl->parse('content.confirm.children.item');
                    }
                    
                    if (count($children) > 0) $this->tpl->parse('content.confirm.children');
                    
                    //get linked as shared content
                    $node_data = $Node->detail($delete_id);
                    $this->tpl->assign("NODE", $node_data);
                    $shared_linked = $Node->getShared($delete_id);
                    
                    foreach ($shared_linked as $linked) {
                        $this->tpl->assign("LINKED", $linked);
                        $this->tpl->parse('content.confirm.linked.item');
                    }
                    
                    if (count($shared_linked) > 0) $this->tpl->parse('content.confirm.linked');
                    
                    
                    $this->tpl->parse('content.confirm');
                
                }
                
            } else {
                
                msg("This can't be deleted", 'error');
            
            }
        }
        
        return true;
    }
}

