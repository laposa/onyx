<?php
/**
 * Copyright (c) 2009-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_scheduler.php');

class Onxshop_Controller_Scheduler extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction()
	{

		$Scheduler = new common_scheduler();
		$Scheduler->setCacheable(false);
	
		if ($Scheduler->anyPendingJobs() && ($lock_token = $Scheduler->lockPendingJobs()) > 0) {

			$jobs = $Scheduler->getLockedJobs($lock_token);

			if (is_array($jobs)) {

				foreach ($jobs as $job_data) {

					$Scheduler->setJobStart($job_data['id']);
					$result = $this->runJob($job_data);
					$messages = trim(strip_tags(urldecode($result->messages)));
					$Scheduler->setJobCompleted($job_data['id'], $result->status, $messages);

				}

			} else {
				msg("Scheduler: Invalid lock token!");
			}

		}

		return true;

	}


	public function runJob($job_data)
	{
		// preprare request URI
		$request = "bo/scheduler/" . $job_data['controller'] . "@bo/scheduler/action_base~node_id={$job_data['node_id']}:" .
			"node_type={$job_data['node_type']}";
		if (strlen($job_data['parameters']) > 0) $request .= ":" . $job_data['parameters'];
		$request .= "~";

		$job = new Onxshop_Request($request);
		$content = $job->getContent();
		$result = json_decode($content);
		if ($result === null) msg("Invalid JSON response from bo/scheduler/" . $job_data['controller'], 2);
		return $result;
	}

}
