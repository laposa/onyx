<?php
/** 
 * Copyright (c) 2009-2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Node extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $node_id = $this->GET['id'];
            
        $_Onyx_Request = new Onyx_Request("node~id=$node_id:fe_edit_mode=preview~");

        $this->tpl->assign('NODE_ID', $node_id);
        $this->tpl->assign('CONTENT', $_Onyx_Request->getContent());

        return true;
    }
}
