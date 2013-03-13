<?php
/** 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Scheduler extends Onxshop_Controller {

	/**
	 * main action
	 */

	public function mainAction()
	{

		// process form request
		if (is_array($_POST['job'])) {

			$url = urlencode($_POST['job']['url']);
	 		$date = $_POST['job']['date'] . " " . $_POST['job']['time'];
	 		$time = strtotime($date);

	 		// try to add new job using scheduler_add component
			$c = new Onxshop_Request("bo/component/scheduler_add~url={$url}:time={$time}~");
			$this->tpl->assign('RESULT', $c->getContent());
		}

		// prepare form variables
		$job = array();

		if (isset($_POST['date'])) $job['date'] = $_POST['date']; 
		else $job['date'] = date("Y-m-d");

		if (isset($_POST['time'])) $job['time'] = $_POST['time']; 
		else $job['time'] = date("H:i:s", time() + 60);

		if (isset($_POST['url'])) $job['url'] = $_POST['url']; 
		else $job['url'] = "http://" . $_SERVER['SERVER_NAME'];

		$this->tpl->assign('JOB', $job);

		// include job list
		$l = new Onxshop_Request("bo/component/scheduler_list");
		$this->tpl->assign('LIST', $l->getContent());

		return true;
	}

	/**
	 * Add job to queue
	 * @param int    $time Timestamp to run the job at
	 * @param string $url  URL to run (using curl) - must be encoded using urlencode()
	 */
	protected function addJob($time, $url)
	{
		// prepare email variables
		require_once('models/common/common_email.php');
		$EmailForm = new common_email();

		$email_recipient = $GLOBALS['onxshop_conf']['global']['admin_email'];
		$name_recipient = $GLOBALS['onxshop_conf']['global']['admin_email_name'];
		$GLOBALS['common_email'] = array(
			'url' => $url, 
			'time' => date("Y-m-d H:i:s", $time), 
		);
		$email_from = false;
		$name_from = "Web server";

		// run at
		$time = date("YmdHi.s", $time);
		$GLOBALS['common_email']['result'] = local_exec("at \"$url\" $time");

		// send email
		$EmailForm->sendEmail('scheduler_add', 'n/a', $email_recipient, $name_recipient, $email_from, $name_from);

		unset($GLOBALS['common_email']);
	}

	/**
	 * Get job detail in a queue
	 */
	protected function getJobDetail($id)
	{
		$id = (int) $id;
		$output = local_exec("atc $id");
		preg_match('/curl (.*)/', $output, $matches);
		return $matches[1];
	}

	/**
	 * List of jobs in a queue
	 */
	protected function listJobs()
	{
		$result = array();
		$output = local_exec("atq");
		$items = explode("\n", $output);

		foreach ($items as $item) {
			if (!empty($item)) {

				$num = "\d+";
				$word = "\w+";
				preg_match("/($num)\s+($word)\s+($word)\s+($num)\s+($num:$num:$num)\s+($num)\s+($word)\s+($word)/", $item, $matches);

				$item = array(
					'number' => $matches[1],
					'date' => "{$matches[2]} {$matches[3]} {$matches[4]} {$matches[6]}",
					'time' => $matches[5],
					'queue' => $matches[7],
					'username' => $matches[8]
				);

				$result[] = $item;
			}
		}

		return $result;
	}

	/**
	 * Remove job from queue
	 */
	protected function removeJob($id)
	{
		$id = (int) $id;
		return local_exec("atrm $id");
	}

}