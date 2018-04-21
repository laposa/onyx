<?php
/** 
 * Copyright (c) 2013-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_scheduler.php');

class Onxshop_Controller_Bo_Component_Scheduler extends Onxshop_Controller {

    /**
     * main action
     */

    public function mainAction()
    {
        $this->Scheduler = new common_scheduler();
        
        if (is_numeric($this->GET['cancel'])) {
            $this->cancelJob($this->GET['cancel']);
        }

        if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
        else $from = 0;
        if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
        else $per_page = 25;

        $jobs = $this->Scheduler->listing("", "scheduled_time DESC", "$from,$per_page");
        $count = $this->Scheduler->count();

        $this->displayListing($jobs);

        $request = new Onxshop_Request("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count~");
        $this->tpl->assign('PAGINATION', $request->getContent());

        return true;
    }


    /**
     * display schedule list
     */
    public function displayListing($jobs)
    {
        $statuses = array("Pending", "In progress", "Completed", "Failed", "Cancelled");

        if (is_array($jobs) && count($jobs) > 0) {
            foreach ($jobs as $job) {
                $job['status_name'] = $statuses[$job['status']];
                $this->tpl->assign("ITEM", $job);
                $this->tpl->parse("content.item");
            }
        } else {
            $this->tpl->parse("content.no_schedule");
        }

        $this->tpl->assign("TOMORROW", date("d/m/Y", time() + 24 * 3600));

    }


    /**
     * cancel job
     */
    public function cancelJob($job_id)
    {
        $this->Scheduler->cancelJob($job_id);
    }

}
