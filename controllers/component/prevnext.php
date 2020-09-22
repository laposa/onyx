<?php
/** 
 * Copyright (c) 2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Component_Prevnext extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * node_id is mandatory input
         */
         
        if (is_numeric($this->GET['node_id'])) {
            $node_id = $this->GET['node_id'];
        } else {
            msg("menu_prevnext: missing node_id", 'error');
            return false;
        }
        
        /**
         * get detail and list
         */
         
        $Node = new common_node();
        
        $first_parent_type_page_node_id = $Node->getParentPageId($node_id);
        
        $parent_page_detail = $Node->getDetail($first_parent_type_page_node_id);
        
        $current_node_detail = $Node->getDetail($node_id);
        
        if ($current_node_detail['node_group'] == 'page') {
            
            // the specific node_id is page type
            $current_page_detail = $current_node_detail;
            $parent_page_detail = $Node->getDetail($Node->getParentPageId($current_page_detail['parent']));
            
        } else {
            
            // we need to first parent type page
            $current_page_detail = $parent_page_detail;
            $parent_page_detail = $Node->getDetail($current_page_detail['parent']);
        }
        
        $siblings = $Node->listing("parent = {$current_page_detail['parent']} AND node_group = 'page' AND publish = 1", 'priority DESC, id ASC');
        
        if (is_array($siblings)) {
            
            /**
             * find prev/next node
             */
             
            foreach ($siblings as $k=>$item) {
            
                if ($item['id'] == $current_page_detail['id']) {
                    
                    $prev_node = $siblings[$k-1];
                    $next_node = $siblings[$k+1];
                    
                    break;
                }
                
            }
            
            /**
             * cycle
             */
             
            if (!is_array($prev_node)) {
                $count = count($siblings);
                $prev_node = $siblings[$count-1];
            }
            
            if (!is_array($next_node)) {
                $next_node = $siblings[0];
            }
        
        }
        
        /**
         * assign
         */
         
        $this->tpl->assign('PREV', $prev_node);
        $this->tpl->assign('ALL', $parent_page_detail);
        $this->tpl->assign('NEXT', $next_node);
        
        return true;
    }
}
