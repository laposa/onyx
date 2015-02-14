<?php
/** 
 * Copyright (c) 2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Author_Stats extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * input
		 */
		 
		if (is_numeric($_POST['customer_id'])) $customer_id = $_POST['customer_id'];
		else $customer_id = 0;
		
		/**
		 * bo users list
		 */
		 
		require_once('models/client/client_customer.php');
		$Customer = new client_customer();
		
		$bo_users_list = $Customer->getCustomersWithRole();
		
		foreach ($bo_users_list as $customer) {
			
			$this->tpl->assign('CUSTOMER', $customer);
			
			if ($customer['id'] == $customer_id) $this->tpl->assign('SELECTED', 'selected="selected"');
			else $this->tpl->assign('SELECTED', '');
			
			$this->tpl->parse('content.item');
			
		}
		
		/**
		 * stats
		 */
		 
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		$author_stats = $Node->getAuthorStats($customer_id);
		
		$this->tpl->assign('AUTHOR_STATS', $author_stats);
		
		return true;
		
	}
}
