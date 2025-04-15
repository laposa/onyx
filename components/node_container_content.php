<?php
/** 
 * Copyright (c) 2013-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * This component displays content from a selected container, for example from a page or layout
 * For example: 
 * {ONYX_REQUEST_special_offer_container0 #component/node_container_content~node_id=1128:container=0~}
 */

class Onyx_Controller_Component_Node_Container_Content extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        
        $node_id = $this->GET['node_id'];
        $container = $this->GET['container'];
        
        
        if (!is_numeric($node_id) || !is_numeric($container)) return false;
        
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        $content = $Node->parseChildren($node_id, $container);

        if (is_array($content)) {
            foreach ($content as $item) {
                $this->tpl->assign('ITEM', $item);
                $this->tpl->parse('content.item');
            }
        }
        
        return true;
        
    }
}
