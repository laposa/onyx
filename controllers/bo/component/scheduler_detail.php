<?php
/** 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once "controllers/bo/component/scheduler.php";

class Onxshop_Controller_Bo_Component_Scheduler_Detail extends Onxshop_Controller_Bo_Component_Scheduler {

	/**
	 * main action
	 */

	public function mainAction()
	{
		$detail = $this->getJobDetail($this->GET['id']);
		$this->tpl->assign("URL", $detail);

		return true;
	}

}