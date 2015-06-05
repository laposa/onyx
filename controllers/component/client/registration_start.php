<?php
/**
 * First step of the registration process
 *
 * Copyright (c) 2008-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Client_Registration_Start extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * include node configuration
		 */
		
		require_once('models/common/common_node.php');
		$node_conf = common_node::initConfiguration();
		//$this->tpl->assign('NODE_CONF', $node_conf);
		
		/**
		 * customer detail
		 */
		 
		require_once('models/client/client_customer.php');
		
		$Customer = new client_customer();
		$Customer->setCacheable(false);
		
		if ($_POST['register']) {
			//check validation of submited fields
			if ($Customer->checkLoginId($_POST['client']['customer'])) {
				$_SESSION['r_client'] = $_POST['client'];
				$this->dispatchToRegistration($node_conf);
				
			} else  {
				msg("User email {$_POST['client']['customer']['email']} is already registered", 'error', 0, 'account_exists');
				$this->tpl->assign('CLIENT', $_POST['client']);
			}
		}

		return true;
	}
	
	/**
	 * send to registration
	 */
	 
	function dispatchToRegistration($node_conf) {
		if ($this->GET['to']) $_SESSION['to'] = $this->GET['to'];
		onxshopGoTo('/page/'.$node_conf['id_map-registration']);
	}
}
