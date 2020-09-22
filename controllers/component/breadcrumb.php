<?php
/** 
 * Copyright (c) 2006-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Component_Breadcrumb extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        
        /**
         * get input variables
         */
        
        $node_id = $this->getNodeId();
        
        if (!is_numeric($node_id)) {
            msg('component/breadcrumb: id must be numeric', 'error', 1);
            return false;
        }
        
        if (is_numeric($this->GET['create_last_link']) && $this->GET['create_last_link'] == 1) $create_last_link = 1;
        else $create_last_link = 0;
        
        /**
         * get detail
         */
         
        $path = $this->Node->getFullPathDetailForBreadcrumb($node_id);
        
        $size = count($path);
        
        /**
         * Get homepage title for page
         */

        $node_home = $this->Node->detail($this->Node->conf['id_map-homepage']);
        $this->tpl->assign('HOME_NODE', $node_home);
        
        if ($path[1]['id'] != $this->Node->conf['id_map-homepage']) {
            $this->tpl->parse('content.item.first');
            $this->tpl->parse('content.item');
        }
        
        $number_of_page_items = 0;
        $i = 1;
        foreach ($path as $p) {
        
            if ($p['node_group'] == 'page') {
            
                if ($i < $size || $create_last_link) $last_item = false;
                else $last_item = true;
                
                if ($last_item) $this->tpl->assign('LAST', 'last');
                else $this->tpl->assign('LAST', '');
                
                $this->tpl->assign('PATH', $p);
                
                if ($i > 1) $this->tpl->parse('content.item.path_delimiter');
                
                if ($i < $size || $create_last_link) $this->tpl->parse('content.item.middle');
                else $this->tpl->parse('content.item.last');
                
                $this->tpl->parse('content.item');
                $number_of_page_items++;
            }
            $i++;
        }
        
        $this->tpl->assign('NUMBER_OF_ITEMS', $number_of_page_items);
        return true;
    }
    
    /**
     * get node id
     */
     
    public function getNodeId() {
    
        if (is_numeric($this->GET['id'])) {
        
            $node_id = $this->GET['id'];
        
            return $node_id;
            
        } else {
            
            return false;
        }
    }
}
