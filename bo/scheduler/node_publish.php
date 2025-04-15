<?php
/** 
 * Copyright (c) 2006-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/scheduler/action_base.php');

class Onyx_Controller_Bo_Scheduler_Node_Publish extends Onyx_Controller_Scheduler_Action_Base {

    /**
     * main action
     */
     
    public function mainAction()
    {
        $node_type = $this->GET['node_type'];
        $node_id = $this->GET['node_id'];

        $this->setPublishStatus($node_type, $node_id, 1);

        return true;
    }

}
