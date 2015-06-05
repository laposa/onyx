<?php
/**
 * Copyright (c) 2007-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Client_Password_Reminder extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	
	public function mainAction() {
		
		/**
		 * client
		 */
		
		require_once('models/client/client_customer.php');
		$Customer = new client_customer();
		$Customer->setCacheable(false);
		
		if ($_POST['submit']) {
			$customer_data = $Customer->getClientByEmail($_POST['client']['customer']['email']);
			
			if (is_array($customer_data)) {	
				require_once('models/common/common_email.php');
		    
		    	$EmailForm = new common_email();
		    			
		    	//this allows use customer data and company data in the mail template
		    	//is passed as DATA to template in common_email->_format
		    	$GLOBALS['common_email']['customer'] = $customer_data;
				
		    	if (!$EmailForm->sendEmail('password_reminder', 'n/a', $customer_data['email'], $customer_data['first_name'] . " " . $customer_data['last_name'])) {
		    		msg("Can't send email with password reminder", 'error');
		    	}
				
				$this->tpl->parse('content.password_sent');
				$hide_form = 1;
			}
		}
		
		if ($hide_form == 0) {
			$this->tpl->parse('content.request_form');
		}
			
		//sanitize before we add HTML attribute checked="checked" :)
		if (is_array($_POST['client'])) {
			$this->tpl->assign('CLIENT', $_POST['client']);
		}

		return true;
	}
}
