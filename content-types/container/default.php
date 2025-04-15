<?php
/**
 * Copyright (c) 2009-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/node/page/default.php');

class Onyx_Controller_Node_Container_Default extends Onyx_Controller_Node {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * include node configuration
         */
        
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        /**
         * nothing to do here, forward first parent page
         */
        
        $first_parent_page_id = $Node->getParentPageId($this->GET['id']);
        
        if (is_numeric($first_parent_page_id) && $first_parent_page_id > 0) {
            
            onyxGoTo("page/" . $first_parent_page_id);
        
        } else {
            
            // there is no parent page to this container, forward to homepage
            onyxGoto("page/" . $Node->conf['id_map-homepage']);
        
        }
        
        return true;
    }
}
