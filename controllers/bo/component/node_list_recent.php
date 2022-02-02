<?php
/**
 * Copyright (c) 2014-2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Node_List_Recent extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        
        $node_list = $Node->listing('', 'modified DESC', '0,500');
                
        foreach ($node_list as $item) {
            
            $item['latest_change_by'] = $Node->getCustomerIdForLastModified($item['id']);
            
            if ($item['publish'] == 0)  $item['class'] = 'disabled';
            $this->tpl->assign("ITEM", $item);
            $this->tpl->parse('content.item');
            
        }
        
        return true;
    }
        
}
