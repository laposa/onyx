<?php
/**
 * Subscribe to newsletter (prepopulated registration)
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/client/registration.php');

class Onxshop_Controller_Component_Client_Newsletter_Subscribe extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * initialize
		 */
		 
		require_once('models/client/client_customer.php');
		
		$Customer = new client_customer();
		$Customer->setCacheable(false);
		
		if (is_array($_POST['client'])) {
			$this->tpl->assign('CLIENT', $_POST['client']);
		}
		
		/**
		 * save
		 */
		 
		if ($_POST['client']['customer']['first_name'] && $_POST['client']['customer']['last_name'] && $_POST['client']['customer']['email']) {
				
			if($id = $Customer->newsletterSubscribe($_POST['client']['customer'])) {	
				msg("Subscribed {$customer['email']}");
				$this->tpl->parse('content.thank_you');
			} else {
				msg("Can't subscribe {$customer['email']}", 'error');
				$this->tpl->parse('content.form');
			}
		} else {
			$this->tpl->parse('content.form');
		}

		return true;
	}
}
