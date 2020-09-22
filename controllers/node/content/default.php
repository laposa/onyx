<?php
/**
 * Copyright (c) 2009-2018 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/node/default.php');

class Onyx_Controller_Node_Content_Default extends Onyx_Controller_Node_Default {

    /**
     * main action
     */
     
    public function mainAction() {
        
        if ($this->processContent()) return true;
        else return false;
        
    }
    
    /**
     * process content
     */
     
    public function processContent() {
        
        $node_id = $this->GET['id'];
        
        if (!is_numeric($node_id)) {
            msg('node/content/default: id not numeric', 'error');
            return false;
        }
        
        require_once('models/common/common_node.php');
        
        $this->Node = new common_node();
        
        $this->node_data = $this->Node->nodeDetail($node_id);
        
        if ($this->node_data['page_title'] == '') {
            $this->node_data['page_title'] = $node_data['title'];
        }
        
        if (!isset($this->node_data['display_title'])) $this->node_data['display_title'] = $GLOBALS['onyx_conf']['global']['display_title'];
        
        $this->tpl->assign("NODE", $this->node_data);
        
        /**
         * load related image with role 'background'
         */
         
        $image = $this->Node->getImageForNodeId($node_id, 'background');
        $this->tpl->assign("IMAGE", $image);
        
        /**
         * display title
         */
         
        $this->displayTitle($this->node_data);


        return true;
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
