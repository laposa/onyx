<?php
/** 
 * Copyright (c) 2015-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onyx_Controller_Node_Content_Page_List extends Onyx_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
    
        parent::mainAction();

        /**
         * parse items
         */

        $node_ids = explode(",", trim($this->node_data['component']['node_ids']));
        
        foreach ($node_ids as $node_id) {

            if (is_numeric($node_id)) {
                
                $item_node_data = $this->Node->nodeDetail($node_id);
                $item_node_data['image'] = $this->Node->getTeaserImageForNodeId($node_id);;

                $this->tpl->assign('ITEM', $item_node_data);
                $this->tpl->parse('content.item');
                
            }

        }

        return true;
    }

}
