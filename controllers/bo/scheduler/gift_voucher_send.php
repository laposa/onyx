<?php
/**
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/scheduler/action_base.php');

class Onxshop_Controller_Bo_Scheduler_Gift_Voucher_Send extends Onxshop_Controller_Scheduler_Action_Base {

	/**
	 * main action
	 */
	 
	public function mainAction()
	{
		$promotion_id = $this->GET['promotion_id'];

		if (is_numeric($promotion_id)) {

			$request = new Onxshop_Request("component/ecommerce/gift_voucher_send~promotion_id={$promotion_id}~");
			$result = $request->getContent();
			$this->setActionStatus(true);

		} else {

			msg("Promotion_id not specified");
			$this->setActionStatus(false);

		}
		
		return true;
	}

}
