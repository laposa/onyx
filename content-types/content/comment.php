<?php
/**
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/content/default.php');

class Onyx_Controller_Node_Content_Comment extends Onyx_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {

        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        
        $node_data = $Node->nodeDetail($this->GET['id']);
        
        $_Onyx_Request = new Onyx_Request("component/comment~node_id={$node_data['id']}:allow_anonymouse_submit={$node_data['component']['allow_anonymouse_submit']}~");
        $node_data['content'] = $_Onyx_Request->getContent();
        
        $this->tpl->assign("NODE", $node_data);
        
        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }
}
