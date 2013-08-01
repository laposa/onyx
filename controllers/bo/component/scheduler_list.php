<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/scheduler.php');

class Onxshop_Controller_Bo_Component_Scheduler_List extends Onxshop_Controller_Bo_Component_Scheduler {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		$this->Scheduler = new common_scheduler();
		$this->node_id =  $this->GET['node_id'];
		$this->node_type =  $this->GET['node_type'];
		
		if (!is_numeric($this->node_id)) return false;

		$jobs = $this->Scheduler->getScheduleForNode($this->node_id, $this->node_type);

		$this->deleteRemovedJobs($jobs);
		$this->saveAddedJobs();

		$this->displayListing($jobs);			
		$this->parseControllerList();			

		return true;
	}


	/**
	 * delete removed schedules
	 */
	public function deleteRemovedJobs($jobs)
	{
		if ($_POST['scheduler_action'] !== 'save') return;

		$old_jobs = (array) $_POST['job'];

		foreach ($jobs as $job) {
			if (!in_array($job['id'], $old_jobs)) $this->Scheduler->cancelJob($job['id']);
		}
	}


	/**
	 * save added schedules
	 */
	public function saveAddedJobs()
	{
		$new_jobs = $_POST['scheduler'];
		if ($_POST['scheduler_action'] !== 'save' || !is_array($new_jobs)) return;

		foreach ($new_jobs['controller'] as $i => $controller) {

			$date = $new_jobs['date'][$i];
			$time = $new_jobs['time'][$i];
			$date = implode("-", array_reverse(explode("/", $date)));
			$scheduled_time = strtotime($date . " " . $time);

			$data = array(
				'node_id' => $this->node_id,
				'node_type' => $this->node_type,
				'controller' => $controller,
				'scheduled_time' => $scheduled_time,
			);

			$id = $this->Scheduler->scheduleNewJob($data);

			if ($id > 0) msg("Scheduled task saved as id=$id");

		}
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
	 * parse all available controllers in dropdown
	 */

	public function parseControllerList()
	{
		$files = $this->getSchedulerActions();

		foreach ($files as $file) {
			$this->tpl->assign("ITEM", $file);
			$this->tpl->parse("content.controller_item");
		}
	}


	/**
	 * Get list of available scheduler actions, i.e. content
	 * of bo/scheduler directory
	 */

	public function getSchedulerActions()
	{
		$files = $this->Scheduler->getSchedulerActions();

		foreach ($files as $file) {
			if (strpos($file, "node") !== FALSE) {
				$result[] = $file;
			}
		}

		return $result;
	}

}
