<?php
/** 
 *
 * Copyright (c) 2009-2020 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/common/common_node.php');

class Onyx_Controller_Bo_Component_Node_Duplicate extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        if (is_numeric($this->GET['id'])) $original_node_id = $this->GET['id'];
        else return false;
        
        $this->Node = new common_node();
        
        $new_node_id = $this->Node->duplicateNode($original_node_id);

        if ($new_node_id) {
            msg("Content successfully duplicated.");

            $_Onyx_Request = new Onyx_Request("node~id=$new_node_id~");
            $this->tpl->assign('NODE_DETAIL', $_Onyx_Request->getContent());
        }

        return $new_node_id > 0;
    }

}
