<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Client_Customer_Edit extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * check input
		 */
		 
		if ($_SESSION['client']['customer']['id'] == 0 && $_SESSION['authentication']['authenticity'] < 1) {
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
		
		$this->Customer = new client_customer();
		$this->Customer->setCacheable(false);
		
		/**
		 * save
		 */
		 
		if (is_array($_POST['client']['customer'])) {
			$data_to_save = $_POST['client']['customer'];
			$data_to_save['id'] = $customer_id;
			$this->saveDetail($data_to_save);
		}
			
		/**
		 * get customer detail
		 */
		
		$customer_detail = $this->Customer->getDetail($customer_id);
		
		if (is_array($customer_detail)) {
			$this->tpl->assign('ITEM', $customer_detail);
		} else {
			msg('controllers/client/customer_detail: cannot get detail', 'error');
		}
		
		return true;
	}
	
	/**
	 * save
	 */
	 
	public function saveDetail($data) {
		
		if ($this->Customer->updateCustomer($data)) msg('saved');
		else msg('failed', 'error');
		
	}
}
