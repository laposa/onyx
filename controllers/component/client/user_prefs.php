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
		
		if (is_numeric($this->GET['customer_id']) && constant('ONXSHOP_IN_BACKOFFICE')) $customer_id = $this->GET['customer_id'];
		else $customer_id = $_SESSION['client']['customer']['id'];	
		if (!is_numeric($customer_id)) return false;
		
		if ($_POST['save']) {
			$_POST['client']['customer']['id'] = $customer_id;
			
			if ($Customer->updateClient($_POST['client'])) {
				msg('Client Data Updated');
			} else {
				msg("Can't update client data", 'error');
			}
			
			//onxshopGoTo($_SESSION['referer'], 2);
		}
		
		
		$client_data = $Customer->getClientData($customer_id);
		
		$client_data['customer']['newsletter'] = ($client_data['customer']['newsletter'] == 1) ? 'checked="checked" ' : '';
		
		$this->tpl->assign('CLIENT', $client_data);
		
		//if we are in backoffice 
		if ($_SESSION['authentication']['authenticity'] > 0) {
		
			//allow to change status
			$this->tpl->assign("SELECTED_status_{$client_data['customer']['status']}", 'selected="selected"');
			$this->tpl->parse('content.status');
			
			//than display plain password 
			$this->tpl->parse('content.password_plain');
		
			//and allow to change account type
			$this->tpl->assign("SELECTED_account_type_{$client_data['customer']['account_type']}", 'selected="selected"');
			$this->tpl->parse('content.account_type');
		}
		
		return true;
	}
}
