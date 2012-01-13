<?php
/**
 * Backoffice customer list filter
 *
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Customer_Filter extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * if submitted search display save button
		 */
		 
		if (isset($_POST['search'])) $this->tpl->parse('content.form.save');
		
		/**
		 * Store submited data to the SESSION
		 */
		
		if (isset($_POST['customer-filter'])) {
			$_SESSION['customer-filter'] = $_POST['customer-filter'];
			$_SESSION['customer-filter']['group_id'] = ''; 
		} else if (is_numeric($_SESSION['customer-filter']['group_id'])) {
			/**
			 * update incase group_id selected
			 */
			$group_id = $_SESSION['customer-filter']['group_id'];
			if ($group_filter = $this->getGroupFilter($group_id)) {
				$_SESSION['customer-filter'] = $group_filter;
				$_SESSION['customer-filter']['group_id'] = $group_id;
			}
		}
		
		/**
		 * populate filter in case it's empty
		 */
		
		if (!is_array($_SESSION['customer-filter'])) {
			$_SESSION['customer-filter'] = array();
			$_SESSION['customer-filter']['invoice_status'] = 0;
			$_SESSION['customer-filter']['account_type'] = -1;
		}
		
		if (trim($_SESSION['customer-filter']['group_name']) == '') $_SESSION['customer-filter']['group_name'] = 'Your new group name';
		
		/**
		 * if submitted save, only process save action and don't display form (exit here)
		 */
		
		if (isset($_POST['save'])) return $this->saveGroupFilter($_SESSION['customer-filter']);
		
		/**
		 * assign to template variable
		 */
		 
		$this->tpl->assign('CUSTOMER_FILTER', $_SESSION['customer-filter']);
		
		/**
		 * With orders and account type options
		 */
		
		$this->tpl->assign("SELECTED_invoice_status_{$_SESSION['customer-filter']['invoice_status']}", "selected='selected'");
		$this->tpl->assign("SELECTED_account_type_{$_SESSION['customer-filter']['account_type']}", "selected='selected'");
		
		/**
		 * Country list
		 */
		 
		require_once('models/international/international_country.php');
		$Country = new international_country();
		$countries = $Country->listing();
		
		foreach ($countries as $item) {
			if ($item['id'] == $_SESSION['customer-filter']['country_id']) $item['selected'] = "selected='selected'";
			else $item['selected'] = '';
			$this->tpl->assign('ITEM', $item);
			$this->tpl->parse('content.form.country.item');
		}
		
		$this->tpl->parse('content.form.country');

		/**
		 * product list
		 */
		
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		
		$product_list = $Product->listing('publish = 1', 'name ASC');
		
		if (is_array($product_list) && count($product_list) > 0) {
		
			foreach ($product_list as $item) {
				
				if (is_array($_SESSION['customer-filter']['product_bought'])) {
					if (in_array($item['id'], $_SESSION['customer-filter']['product_bought'])) $item['selected'] = "selected='selected'";
					else $item['selected'] = '';
				} else {
					$item['selected'] = '';
				}
				
				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.form.product.item');
			}
		
			$this->tpl->parse('content.form.product');
		}
		
		$this->tpl->parse('content.form');
		
		return true;
	}
	
	/**
	 * save group filter
	 */
	 
	public function saveGroupFilter($filter) {
		
		if (!is_array($filter)) return false;
		
		require_once('models/client/client_group.php');
		$ClientGroup = new client_group();
		
		$data = array();
		if (is_numeric($_SESSION['customer-filter-selected_group_id'])) $data['id'] = $_SESSION['customer-filter-selected_group_id'];
		$data['name'] = $filter['group_name'];
		$data['description'] = '';
		$data['search_filter'] = $filter;
		
		/**
		 * save actual group
		 */
		 
		if ($id = $ClientGroup->saveGroup($data)) {
			msg("Customers group saved under name {$data['name']} and ID $id");
			/**
			 * move customer to this group
			 */
		
			$this->moveCustomersToGroup($id);
		
		} else {
			msg("Cannot save customers group", 'error');
		}
		
		return true;
		
	}

	/**
	 * get group filter
	 */
	 
	public function getGroupFilter($group_id) {
		
		if (!is_numeric($group_id) || $group_id < 1) return false;
		
		require_once('models/client/client_group.php');
		$ClientGroup = new client_group();
		
		$group_detail = $ClientGroup->getDetail($group_id);

		if (is_array($group_detail['search_filter']) && count($group_detail['search_filter']) > 0) {
			return $group_detail['search_filter'];
		} else {
			return false;
		}
	}
	
	/**
	 * move customers to group
	 */
	 
	public function moveCustomersToGroup($group_id) {
	
		require_once('models/client/client_group.php');
		require_once('models/client/client_customer.php');
		$ClientGroup = new client_group();
		$Customer = new client_customer();
		//force cache even for back office user
		$Customer->setCacheable(true);
		
		if ($group_filter = $this->getGroupFilter($group_id)) {
		
			$customer_list = $Customer->getClientList(0, $group_filter);
			
			$list_count = count($customer_list);
			
			if ($Customer->moveCustomersToGroupFromList($customer_list, $group_id)) {
				msg("All $list_count customers were moved to group ID $group_id");
				//flush cache as we are using forced cache for client_customer in backoffice
				$Customer->flushCache();
			} else {
				msg("Cannot move $list_count customers to group ID $group_id", 'error');
				return false;
			}
			
		} else {
			return false;
		}
		
	}
}
