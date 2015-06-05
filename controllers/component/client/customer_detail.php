<?php
/**
 * Copyright (c) 2010-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Client_Customer_Detail extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * check input
		 */
		 
		if ($_SESSION['client']['customer']['id'] == 0 && !Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
			msg('controllers/client/customer_detail: You must logged in.', 'error');
			onxshopGoTo("/");
		} else {
			if (is_numeric($this->GET['customer_id']) && constant('ONXSHOP_IN_BACKOFFICE')) $customer_id = $this->GET['customer_id'];
			else $customer_id = $_SESSION['client']['customer']['id'];	
		}
		
		if (!is_numeric($customer_id)) return false;
		
		/**
		 * initialize
		 */
		 
		require_once('models/client/client_customer.php');
		
		$Customer = new client_customer();
		$Customer->setCacheable(false);
		
		/**
		 * get customer detail
		 */
		
		$customer_detail = $Customer->getDetail($customer_id);
		
		if (is_array($customer_detail)) {
			$this->tpl->assign('ITEM', $customer_detail);
		} else {
			msg('controllers/client/customer_detail: cannot get detail', 'error');
		}
		
		return true;
	}
}
