<?php
/**
 *
 * Copyright (c) 2011-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Client_Customer_Search extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * initialise groups
		 */
		 
		require_once('models/client/client_group.php');
		$ClientGroup = new client_group();
		
		/**
		 * Read data
		 */
		
		if (isset($_POST['customer-filter'])) $customer_filter = $_POST['customer-filter'];
		else if (is_array($_SESSION['bo']['customer-filter'])) $customer_filter = $_SESSION['bo']['customer-filter'];
		else $customer_filter = false;
		
		// default values
		if (!is_array($customer_filter)) {
			$customer_filter = array();
			$customer_filter['invoice_status'] = 0;
			$customer_filter['account_type'] = -1;
		}
		
		//HACK: save selected group for use in filter update
		$_SESSION['bo']['customer-filter-selected_group_id'] = $customer_filter['group_id'];
		
		/**
		 * list groups
		 */
		 
		$list = $ClientGroup->listGroups();
		
		foreach ($list as $item) {
			
			$this->tpl->assign('ITEM', $item);
			
			if ($item['id'] == $customer_filter['group_id']) $this->tpl->assign('SELECTED', 'selected="selected"');
			else $this->tpl->assign('SELECTED', '');
			
			$this->tpl->parse('content.item');
		}
		
		// dropdowns
		$this->tpl->assign("SELECTED_group_{$customer_filter['group_id']}", 'selected="selected"');
		$this->tpl->assign("SELECTED_account_type_{$customer_filter['account_type']}", 'selected="selected"');
		
		// checkboxes
		if (is_numeric($customer_filter['backoffice_role_only']) && $customer_filter['backoffice_role_only'] == 1) $this->tpl->assign('CHECKED_backoffice_role_only', 'checked="checked"');
		
		/**
		 * save to the SESSION
		 */
		
		$_SESSION['bo']['customer-filter'] = $customer_filter;
		
		return true;
	}
}
