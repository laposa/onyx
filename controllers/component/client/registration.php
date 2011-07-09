<?php
/**
 * Registration controller
 *
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Client_Registration extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * autopopulate
		 */
		 
		if (is_array($_SESSION['r_client']) && !is_array($_POST['client'])) {
			$_POST['client'] = $_SESSION['r_client'];
		}
		
		/**
		 * initialize
		 */
		
		require_once('models/client/client_customer.php');
		
		$Customer = new client_customer();
		$Customer->setCacheable(false);
		
		
		//if ($_POST['client']['customer']['email'])  $Customer->checkEmail($_POST['client']['customer']['email']);
		
		/**
		 * country list
		 */
		 
		require_once('models/international/international_country.php');
		$Country = new international_country();
		$countries = $Country->listing();
		
		if (!isset($_POST['client']['address']['delivery']['country_id'])) $_POST['client']['address']['delivery']['country_id'] = $Country->conf['default_id'];
		// address will be caught through relation
		//delivery
		foreach ($countries as $c) {
			if ($c['id'] == $_POST['client']['address']['delivery']['country_id']) $c['selected'] = "selected='selected'";
			else $c['selected'] = '';
			$this->tpl->assign('COUNTRY', $c);
			$this->tpl->parse('content.country_delivery.item');
		}
		$this->tpl->parse('content.country_delivery');
		
		
		/**
		 * save
		 */
		 
		if ($_POST['save']) {
			
			$client_customer = $_POST['client']['customer'];
			$client_address = $_POST['client']['address'];
			$client_company = $_POST['client']['company'];
			
			if (is_numeric($client_customer['trade'])) $client_customer['account_type'] = 1; //requested trade account
			unset($client_customer['trade']);
			unset($client_customer['password1']);
			
			//check validation of submited fields
			if ($Customer->prepareToRegister($client_customer) && $this->checkPasswordMatch($_POST['client']['customer']['password'], $_POST['client']['customer']['password1'])) {
				
				// when required some other step for registering, store fields in session
				//$_SESSION['r_client'] = $_POST['client'];
				
				if (trim($client_address['delivery']['name']) == '') {
					$client_address['delivery']['name'] = "{$client_customer['title_before']} {$client_customer['first_name']} {$client_customer['last_name']}";
				}
				
				/**
				 * register
				 */
				
				if($id = $Customer->registerCustomer($client_customer, $client_address, $client_company)) {
				
					msg("Registration of customer ID $id was successful");
					
					/**
					 * login
					 */
					 
					$this->login($Customer);
					
					
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
		
		
		$this->tpl->assign('CLIENT', $_POST['client']);

		return true;
	}
	
	
	/**
	 * login
	 */
	 
	public function login($Customer) {
		
		//TODO: implement login by username option
		$username = $_POST['client']['customer']['email'];
		$md5_password = md5($_POST['client']['customer']['password']);
		
		$customer_detail = $Customer->login($username, $md5_password);
		
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
				//$this->tpl->parse('content.userbox');
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

}
