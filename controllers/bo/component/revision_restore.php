<?php
/**
 * Copyright (c) 2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Revision_Restore extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        $Node = new common_node();
        require_once('models/common/common_revision.php');
        $Revision = new common_revision();

        if (is_numeric($this->GET['id'])) $id = $this->GET['id'];
        $revision_detail = $Revision->detail($id);

        if(isset($revision_detail['id'])) {
            return $Node->restoreRevision($revision_detail);
        } else {
            return false;
        }
    }
    
}

