<?php
/**
 * Registration controller
 *
 * Copyright (c) 2005-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Client_Registration extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		$this->commonAction();
		
		if ($_POST['save']) $this->saveForm();

		$this->generateCountryList();
		$this->prepareCheckboxes();		
		
		$client_data = $_POST['client'];
		
		// format birthday only if available to avoid 01/01/1970 by default
		if ($client_data['customer']['birthday'] != '') $client_data['customer']['birthday'] = strftime('%d/%m/%Y', strtotime($client_data['customer']['birthday']));
		
		$this->tpl->assign('CLIENT', $client_data);

		return true;
	}
	

	/**
	 * process input form 
	 */
	public function saveForm()
	{
		$client_customer = $_POST['client']['customer'];
		$client_address = $_POST['client']['address'];
		$client_company = $_POST['client']['company'];
		
		if (is_numeric($client_customer['trade'])) $client_customer['account_type'] = 1; //requested trade account
		unset($client_customer['trade']);
		unset($client_customer['password1']);
		
		/**
		 * check password match for non-social account
		 */
		 
		if (!$this->Customer->isSocialAccount($_POST['client']['customer'])) {
			
			//only if repeat password is actually provided	
			if ($_POST['client']['customer']['password'] && $_POST['client']['customer']['password1']) $password_match_status = $this->checkPasswordMatch($_POST['client']['customer']['password'], $_POST['client']['customer']['password1']);
			else $password_match_status = true;
			
		} else {
		
			$password_match_status = true;
			
		}
			
		//check validation of submited fields
		if ($this->Customer->prepareToRegister($client_customer) && $password_match_status) {
		
			// when required some other step for registering, store fields in session
			//$_SESSION['r_client'] = $_POST['client'];
			if (is_array($client_address) && trim($client_address['delivery']['name']) == '') {
				$client_address['delivery']['name'] = "{$client_customer['title_before']} {$client_customer['first_name']} {$client_customer['last_name']}";
			}
			
			/**
			 * register
			 */
			
			if($id = $this->Customer->registerCustomer($client_customer, $client_address, $client_company)) {
			
				msg("Customer registration successfully completed");
				
				/**
				 * login
				 */
				 
				if ($this->Customer->isSocialAccount($client_customer)) {
					
					//msg("social account", 'ok', 1);
					$this->login($client_customer['email']);
					
				} else {
					//msg("not social account", 'ok', 1);
					$this->login($client_customer['email'], $client_customer['password']);
					
				}
				
				// auto login (TODO allow to enable/disable this behaviour)
				$this->Customer->generateAndSaveOnxshopToken($id);
				
				/**
				 * forward
				 */
				 
				$this->forwardAfterLogin();
				
			} else {
				msg('Please complete all required fields marked with a asterisk (*)', 'error');
			}
		
		} else {
		
			msg('Please complete all required fields marked with an asterisk (*)', 'error');
		
		}
	}
	
	/**
	 * login
	 */
	 
	public function login($username, $password = null) {
		
		if (is_null($password)) {
		
			$customer_detail = $this->Customer->login($username);
		
		} else {
		
			$md5_password = md5($password);
			$customer_detail = $this->Customer->login($username, $md5_password);
		}
		
		if ($customer_detail) {
			$_SESSION['client']['customer'] = $customer_detail;
		} else {
			msg('Login from registration failed. Please try again.', 'error');
		}
	}
	
	/**
	 * forward action
	 */
	 
	public function forwardAfterLogin() {
		
		/**
		 * include node configuration
		 */

		require_once('models/common/common_node.php');
		$node_conf = common_node::initConfiguration();
		//$this->tpl->assign('NODE_CONF', $node_conf);
		
		/**
		 * check
		 */
		 
		if ($this->GET['to']) {
			if ($this->GET['to'] == 'ajax') {
				return true;
			} else onxshopGoTo($this->GET['to']);
		} else if ($_SESSION['to']) {
			$to = $_SESSION['to'];
			$_SESSION['to'] = false;
			onxshopGoTo($to);
		} else {
			onxshopGoTo("page/" . $node_conf['id_map-myaccount']);
		}
			
	}
	
	/**
	 * check password match
	 */
	 
	public function checkPasswordMatch($password, $password1) {
	
		if ($password == $password1) {
			return true;
		} else {
			msg("Passwords does not match.", 'error');
			return false;
		}
			
	}
	
	/**
	 * commonAction
	 */
	
	public function commonAction() {
	
		/**
		 * initialize
		 */
		 
		require_once('models/client/client_customer.php');
		$this->Customer = new client_customer();
		$this->Customer->setCacheable(false);
		
		/**
		 * autopopulate
		 */
		 
		if (is_array($_SESSION['r_client']) && !is_array($_POST['client'])) {
			//populate all available fields
			$_POST['client'] = $_SESSION['r_client'];
		
		} else if (is_array($_SESSION['r_client']) && is_array($_POST['client'])) {
			//populate only (hidden) social fields
			$_POST['client']['customer']['facebook_id'] = $_SESSION['r_client']['customer']['facebook_id'];
			$_POST['client']['customer']['twitter_id'] = $_SESSION['r_client']['customer']['twitter_id'];
			$_POST['client']['customer']['google_id'] = $_SESSION['r_client']['customer']['google_id'];
			$_POST['client']['customer']['profile_image_url'] = $_SESSION['r_client']['customer']['profile_image_url'];
		
		}
		
		/**
		 * show password input only to non-social auth
		 */
		 
		if (!$this->Customer->isSocialAccount($_POST['client']['customer'])) {
			$this->tpl->parse('content.password');
		}
				
	}
	
	/**
	 * generateCountryList
	 */
	 
	public function generateCountryList() {
	
		/**
		 * country list
		 */
		 
		require_once('models/international/international_country.php');
		$Country = new international_country();
		$countries = $Country->listing("", "name ASC");
		
		if (!isset($_POST['client']['address']['delivery']['country_id'])) $_POST['client']['address']['delivery']['country_id'] = $Country->conf['default_id'];
		// address will be caught through relation
		//delivery
		foreach ($countries as $c) {

			if ($c['publish'] == 1) {

				if ($c['id'] == $_POST['client']['address']['delivery']['country_id']) $c['selected'] = "selected='selected'";
				else $c['selected'] = '';
				$this->tpl->assign('COUNTRY', $c);
				$this->tpl->parse('content.country_delivery.item');
			}
		}
		$this->tpl->parse('content.country_delivery');


	}
	
	/**
	 * prepareCheckboxes
	 */
	 
	public function prepareCheckboxes() {
	
		/**
		 * prepare for output
		 */
		 
		if(isset($_POST['client']['customer']['newsletter'])) {
			$_POST['client']['customer']['newsletter'] = ($_POST['client']['customer']['newsletter'] == 1) ? 'checked="checked" ' : '';
		} else {
			$_POST['client']['customer']['newsletter'] = 'checked="checked" ';
		}

		if(isset($_POST['client']['customer']['trade'])) {
			$_POST['client']['customer']['trade'] = ($_POST['client']['customer']['trade'] == 1) ? 'checked="checked" ' : '';
		} else {
			$_POST['client']['customer']['trade'] = '';
		}


	}

}
