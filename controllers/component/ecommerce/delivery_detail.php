<?php
/**
 * Transaction Detail
 *
 * Copyright (c) 2008-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Ecommerce_Delivery_Detail extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * Input data
		 */
		
		if (is_numeric($this->GET['id'])) $order_id = $this->GET['id'];
		else return false;
		
		/**
		 * Create objects
		 */
		require_once('models/ecommerce/ecommerce_order.php');
		$Order = new ecommerce_order();
		
		require_once('models/ecommerce/ecommerce_delivery.php');
		$Delivery = new ecommerce_delivery();
		
		/**
		 * Get details for order to be able make a security check
		 */
		if (is_numeric($order_id)) $order_data = $Order->getOrder($order_id);
		
		//security check of owner
		if ($order_data['basket']['customer_id'] !== $_SESSION['client']['customer']['id'] &&  !Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
			msg('unauthorized access to view transaction detail', 'error');
		} else {
			$delivery_list = $Delivery->getDeliveryListByOrderId($order_id);
			
			//print_r($transaction_list);
			if (is_array($delivery_list)) {
				foreach ($delivery_list as $item) {
					$item['other_data'] = unserialize($item['other_data']);
					if ($item['customer_note'] == "") $item['customer_note'] = 'n/a';
					$this->tpl->assign('ITEM', $item);
					$this->tpl->parse('content.item');
				}
			}
		}

		return true;
	}
}
