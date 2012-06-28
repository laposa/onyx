<?php
/**
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Client_Login extends Onxshop_Controller {

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
		
		if ($_SESSION['client']['customer']['id'] > 0 && !$this->GET['client']['email']) {
		
			//msg('you are in');
			//onxshopGoTo($this->GET['to']);
		
		} else {
		
			/* Check if user has been remembered */
			if(isset($_COOKIE['autologin_username']) && isset($_COOKIE['autologin_md5_password'])) {
			
				$customer_detail = $Customer->login($_COOKIE['autologin_username'], $_COOKIE['autologin_md5_password']);
				
				if ($customer_detail) {
					$_SESSION['client']['customer'] = $customer_detail;
				
				} else {
				
					msg("Autologin of ({$_COOKIE['autologin_username']}) failed", 'error', 1);
				
				}
			}
		
			/* client submitted username/password */
			if (isset($_POST['login'])) {
			
				$customer_detail = $Customer->login($_POST['client']['username'], md5($_POST['client']['password']));
			
				if ($customer_detail) {
				
					$_SESSION['client']['customer'] = $customer_detail;
					
					/**
					 * If the user has requested that we remember that
					 * he's logged in, so we set two cookies. One to hold his username,
					 * and one to hold his md5 encrypted password. We set them both to
					 * expire in 100 days. Now, next time he comes to our site, we will
					 * log him in automatically.
					 */
				
					if(isset($_POST['autologin'])){
						
						setcookie("autologin_username", $_SESSION['client']['customer']['email'], time()+60*60*24*100, "/");
						//passwords are already md5 in the database
						setcookie("autologin_md5_password", $_SESSION['client']['customer']['password'], time()+60*60*24*100, "/");
					
					}
					
				} else {
					
					$this->loginFailed();
					
				}
				
			}
			
			/* log in as client from backoffice */
			if ($_SESSION['authentication']['authenticity'] > 0 && $this->GET['client']['email']) {
				
				$customer_detail = $Customer->getClientByEmail($this->GET['client']['email']);
				
				if ($customer_detail) {
				
					$_SESSION['client']['customer'] = $customer_detail;
				
				} else {
				
					msg('Login from backoffice failed.', 'error');
				
				}
			}
		}
		
		if ($_SESSION['client']['customer']['id'] > 0 && is_numeric($_SESSION['client']['customer']['id'])) {
			
			//update basket
			if ($_SESSION['basket']['id'] > 0 && is_numeric($_SESSION['basket']['id'])) {
			
				require_once('models/ecommerce/ecommerce_basket.php');
				$Basket = new ecommerce_basket();
				$Basket->setCacheable(false);
				
				$basket_data = $Basket->detail($_SESSION['basket']['id']);
				$basket_data['customer_id'] = $_SESSION['client']['customer']['id'];
				if (!$Basket->update($basket_data)) msg('Basket updated failed', 'error');
			}
			
			msg('you are successfully in', 'ok', 2);
			
			/**
			 * forward
			 */
			
			$this->forwardAfterLogin();
			
		}
		
		//output
		$this->tpl->assign('CLIENT', $_POST['client']);
		$this->tpl->parse('content.login_box');
		
		return true;
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
				$this->tpl->parse('content.userbox');
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
	 * login failed action
	 */
	 
	public function loginFailed() {
	
		msg('Login failed.  Please try again.', 'error');
	
	}
	
}
