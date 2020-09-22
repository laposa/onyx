<?php
/**
 * Copyright (c) 2012 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/layout/default.php');

class Onxshop_Controller_Node_Layout_Tabs extends Onxshop_Controller_Node_Layout_Default {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * check input: node id value must be numeric
         */
         
        if (!is_numeric($this->GET['id'])) {
            msg("node/layout/default: id not numeric", 'error');
            return false;
        }
        
        /**
         * layout standard functions
         */
         
        $this->processContainers();
        $this->processLayout();
        
        /**
         * custom functions
         */
         
        $this->generateTabsMenu($this->GET['id']);
    
        return true;
            
    }
    
    /**
     * generateTabsMenu
     */
     
    public function generateTabsMenu($node_id) {
    
        if (!is_numeric($node_id)) return false;
        
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        /**
         * get list of children
         */
         
        $children = $Node->getChildren($node_id, 'priority DESC, id ASC');
        
        if (!is_array($children)) return false;
        
        /**
         * filter only published items
         */
        
        $children_published = array();
        
        foreach ($children as $item) {
        
            if ($item['publish'] == 1) $children_published[] = $item;
        
        }
        
        /**
         * show only if any items are left
         */
         
        if (is_array($children_published) && count($children_published) > 0) {

            $total_count = count($children_published);
            
            foreach ($children_published as $k=>$item) {
                $first_last = '';
                if ($k == 0) $first_last = 'first';
                if ($k == ($total_count - 1)) $first_last .= ' last';
                $this->tpl->assign('FIRST_LAST', $first_last);
                $this->tpl->assign('ITEM', $item);
                $this->tpl->parse('content.menu.item');
            }
            
            $this->tpl->parse('content.menu');
            
        }
        
        return true;
    }
    
}