<?php
/**
 * Copyright (c) 2009-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/node/default.php');

class Onxshop_Controller_Node_Layout_Default extends Onxshop_Controller_Node_Default {

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

        return true;
    }
    
    /**
     * process layout
     */
     
    public function processLayout() {
        
        require_once('models/common/common_node.php');
        
        $this->Node = new common_node();
        
        $node_data = $this->Node->nodeDetail($this->GET['id']);
        
        if ($node_data['page_title'] == '') {
            $node_data['page_title'] = $node_data['title'];
        }
        
        if (!isset($node_data['display_title'])) $node_data['display_title'] = $GLOBALS['onxshop_conf']['global']['display_title'];
        
        $this->tpl->assign("NODE", $node_data);
        
        /**
         * display title
         */
         
        $this->displayTitle($node_data);
        
    }
    
    /**
     * display title
     */
     
    public function displayTitle($node_data) {
 
        if ($node_data['display_title'])  {
            //if ($node_data['link_to_node_id'] > 0) $this->tpl->parse('content.title_link');
            $this->tpl->parse('content.title');
        }
        
    }
    
}
