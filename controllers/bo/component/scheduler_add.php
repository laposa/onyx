<?php
/** 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once "controllers/bo/component/scheduler.php";

class Onxshop_Controller_Bo_Component_Scheduler_Add extends Onxshop_Controller_Bo_Component_Scheduler {

	/**
	 * main action
	 *
	 * required input variables:
	 * 	url - URL to run, has to be local url
	 * 	time - unix timestamp to schedule the job at (integer)
	 */

	public function mainAction()
	{
		if (ONXSHOP_ALLOW_SCHEDULER !== true) {
			msg('Sorry, scheduler feature is disabled in your installation', 'error');
			return true;
		}

		$baseUrl = "http://" . $_SERVER['SERVER_NAME'];
		$url = $this->GET['url'];
		$time = $this->GET['time'];

		if (strlen($url) > 0) {

			if (substr($url, 0, strlen($baseUrl)) == $baseUrl) {

				if ($time > time()) {

					$this->addJob($time, $url);
					msg("Job has been scheduled.", "ok", 1);

				} else msg("Given time is invalid or not in the future.", "error", 1);

			} else msg("For security reasons you can schedule only local URLs.", "error", 1);

		} else msg("URL not specified", "error", 1);

		return true;
	}

}