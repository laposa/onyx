<?php
/** 
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Node extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $node_id = $this->GET['id'];
            
        $_Onyx_Request = new Onyx_Request("node~id=$node_id~");
        $node_data['content'] = $_Onyx_Request->getContent();
        $this->tpl->assign('NODE', $node_data);

        return true;
    }
}
