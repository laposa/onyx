<?php
/**
 * Copyright (c) 2005-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('models/client/client_customer.php');
require_once('models/client/client_customer_token.php');

class Onxshop_Controller_Component_Client_Login extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * client
		 */
		 
		$Customer = new client_customer();
		$Customer->setCacheable(false);
		
		if ($_SESSION['client']['customer']['id'] > 0 && !$this->GET['client']['email']) {
		
			//msg('you are in');
			//onxshopGoTo($this->GET['to']);
		
		} else {
		
			/* client submitted username/password */
			if (isset($_POST['login'])) {
			
				$customer_detail = $Customer->login($_POST['client']['customer']['email'], md5($_POST['client']['customer']['password']));
			
				if ($customer_detail) {
				
					$_SESSION['client']['customer'] = $customer_detail;
					
					if (isset($_POST['autologin'])) {
						
						// auto login (TODO allow to enable/disable this behaviour globally)
						$Customer->generateAndSaveOnxshopToken($customer_detail['id']);
					
					}
					
				} else {
					
					$this->loginFailed();
					
				}
				
			}
			
			/* log in as client from backoffice */
			if (Onxshop_Bo_Authentication::getInstance()->isAuthenticated() && $this->GET['client']['email']) {
				
				$customer_detail = $Customer->getClientByEmail($this->GET['client']['email']);
				
				if ($customer_detail) {
				
					$_SESSION['client']['customer'] = $customer_detail;
				
				} else {
				
					msg('Login from backoffice failed.', 'error');
				
				}
			}
		}
		
		/**
		 * check status
		 */
		 
		if ($_SESSION['client']['customer']['id'] > 0 && is_numeric($_SESSION['client']['customer']['id'])) {
			
			$this->actionAfterLogin();
			
		}
				
		//output
		$this->tpl->assign('CLIENT', $_POST['client']);
		$this->tpl->parse('content.login_box');
		
		return true;
	}
	
	/**
	 * actionAfterLogin
	 */
	 
	public function actionAfterLogin() {
		
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
		
		$this->tpl->assign('FAILED', 'failed');
		msg(I18N_LOGIN_FAILED, 'error');
	
	}
	
}
