<?php
/**
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Comment extends Onxshop_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {

        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        
        $node_data = $Node->nodeDetail($this->GET['id']);
        
        $_Onxshop_Request = new Onxshop_Request("component/comment~node_id={$node_data['id']}:allow_anonymouse_submit={$node_data['component']['allow_anonymouse_submit']}~");
        $node_data['content'] = $_Onxshop_Request->getContent();
        
        $this->tpl->assign("NODE", $node_data);
        
        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }
}
