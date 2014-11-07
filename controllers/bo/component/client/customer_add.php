<?php
/** 
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Client_Customer_Add extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		if ($customer_data = $_POST['client']['customer']) {
			
			require_once 'models/client/client_customer.php';
			$Customer = new client_customer();
			
			if ($id = $Customer->registerCustomer($customer_data)) {
				
				onxshop_flush_cache();
				onxshopGoTo("/backoffice/customers/$id/detail");
				
			} else {
				
				msg("Cannot add user", 'error');
				
			}
			
		}

		return true;
		
	}
}
