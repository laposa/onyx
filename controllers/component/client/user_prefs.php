<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Client_User_Prefs extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		if ($_SESSION['client']['customer']['id'] == 0 && $_SESSION['authentication']['authenticity'] < 1) {
			msg('user_prefs: You must be logged in first.', 'error');
			onxshopGoTo("/");
		}
		
		require_once('models/client/client_customer.php');
		$Customer = new client_customer();
		$Customer->setCacheable(false);
		
		$customer_id = $_SESSION['client']['customer']['id'];	
		if (!is_numeric($customer_id)) return false;
		
		if ($_POST['save']) {
			$_POST['client']['customer']['id'] = $customer_id;

			// do not allow to set certain properties			
			unset($_POST['client']['customer']['status']);
			unset($_POST['client']['customer']['group_id']);
			unset($_POST['client']['customer']['account_type']);
			unset($_POST['client']['customer']['other_data']);

			if ($Customer->updateClient($_POST['client'])) {
				msg('Client Data Updated');
			} else {
				msg("Can't update client data", 'error');
			}
			
		}
		
		$client_data = $Customer->getClientData($customer_id);
		
		$client_data['customer']['newsletter'] = ($client_data['customer']['newsletter'] == 1) ? 'checked="checked" ' : '';
		
		$this->tpl->assign('CLIENT', $client_data);
		
		return true;
	}
}
