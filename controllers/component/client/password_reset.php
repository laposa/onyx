<?php
/**
 * Copyright (c) 2006-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Client_Password_Reset extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	
	public function mainAction() {
		
		/**
		 * initialise client_customer object
		 */
		
		require_once('models/client/client_customer.php');
		$Customer = new client_customer();
		$Customer->setCacheable(false);
		
		/**
		 * process when submited
		 */
		 
		if ($_POST['submit']) {
		
			/**
			 * assign first
			 */
			 
			if (is_array($_POST['client'])) {
				$this->tpl->assign('CLIENT', $_POST['client']);
			}
		
			/**
			 * get detail
			 */
			 
			$customer_data = $Customer->getClientByEmail($_POST['client']['customer']['email']);
			
			/**
			 * when real client, get key
			 */
			 
			if (is_array($customer_data)) {
				$current_key = $Customer->getPasswordKey($_POST['client']['customer']['email']);
				$customer_data['password_key'] = $current_key;
			}
			
			/**
			 * if key was generated successfully, than send it by email
			 */
			 
			if ($current_key) {
			
				require_once('models/common/common_email.php');
				$EmailForm = new common_email();
			
				//this allows use customer data and company data in the mail template
				//is passed as DATA to template in common_email->_format
				$GLOBALS['common_email']['customer'] = $customer_data;
				
				if (!$EmailForm->sendEmail('request_password_change', 'n/a', $customer_data['email'], $customer_data['first_name'] . " " . $customer_data['last_name'])) {
					msg("Can't send email with request for password reset", 'error');
				}
				
				$this->tpl->parse('content.request_sent');
				$hide_form = 1;
			}
		}
		
		/**
		 * reset password when valied email and key is provided
		 */
		 
		if  ($this->GET['email'] && $this->GET['key']) {
		
			if ($Customer->resetPassword($this->GET['email'], $this->GET['key'])) {
				msg("Password for {$this->GET['email']} has for been renewed.", 'ok', 2);
				$this->tpl->parse('content.password_changed');
				$hide_form = 1;
			}
		
		}
		
		/**
		 * conditional display form
		 */
		
		if ($hide_form == 0) {
			$this->tpl->parse('content.request_form');
		}

		return true;
	}
}
