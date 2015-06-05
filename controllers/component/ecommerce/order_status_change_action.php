<?php
/** 
 * Copyright (c) 2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/order_status_change.php');

class Onxshop_Controller_Component_Ecommerce_Order_Status_Change_Action extends Onxshop_Controller_Component_Ecommerce_Order_Status_Change {
	
	/**
	 * customStatusChangeAction
	 */
	 
	public function customStatusChangeAction($order_id, $status) {
		
		if (!is_numeric($order_id) || !is_numeric($status)) return false;
		
		/**
		 * add your action here, e.g. send to warehouse
		 */
		/*
		if ($status == 1) {
			$_Onxshop_Request = new Onxshop_Request("component/ecommerce/your_warehouse_integration_controller~order_id={$order_id}~");
		}*/
		
		return true;
		
	}
}
