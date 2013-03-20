<?php
/** 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
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

		$jobs = $this->Scheduler->listing("", "scheduled_time DESC");
		$this->displayListing($jobs);			

		return true;
	}


	/**
	 * display schedule list
	 */
	public function displayListing(&$jobs)
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

	}


	/**
	 * cancel job
	 */
	public function cancelJob($job_id)
	{
		$this->Scheduler->cancelJob($job_id);
	}

}