<?php
/** 
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Order_Status_Change_Action extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		if (!is_numeric($this->GET['order_id']) || !is_numeric($this->GET['status'])) {
			msg("Onxshop_Controller_Component_Ecommerce_Order_Status_Change_Action: order_id or status isn't numeric");
			return false;
		}
		
		$order_id = $this->GET['order_id'];
		$status = $this->GET['status'];
		
		/**
		 * add your action here, e.g. send to warehouse
		 */
		/*
		if ($status == 1) {
			$_nSite = new nSite("component/ecommerce/your_warehouse_integration_controller~order_id={$order_id}~");
		}*/

		return true;
		
	}
}
