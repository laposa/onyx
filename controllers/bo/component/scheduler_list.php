<?php
/** 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once "controllers/bo/component/scheduler.php";

class Onxshop_Controller_Bo_Component_Scheduler_List extends Onxshop_Controller_Bo_Component_Scheduler {

	/**
	 * main action
	 */

	public function mainAction()
	{
		if (is_numeric($this->GET['remove'])) {
			$this->removeJob($this->GET['remove']);
		}

		$list = $this->listJobs();

		if (count($list) > 0) {

			foreach ($list as $item) {
				$this->tpl->assign("ITEM", $item);
				$this->tpl->parse("content.item");
			}

		} else $this->tpl->parse("content.no_jobs");

		return true;
	}

}