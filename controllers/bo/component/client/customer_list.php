<?php
/**
 * Backoffice customer list
 *
 * Copyright (c) 2008-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Client_Customer_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/client/client_customer.php');	
		require_once('models/client/client_customer_taxonomy.php');	
		$Customer = new client_customer();
		$Taxonomy = new client_customer_taxonomy();
			 
		/**
		 * Filtering
		 */
		
		$customer_filter = $_SESSION['bo']['customer-filter'];
		
		/**
		 * Sorting
		 */
		
		$order_by = $this->getOrderBy();
					
		//msg("Sorted by $order_by");
		
		/**
		 * Initialize pagination variables
		 */
		
		if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
		else $from = 0;
		if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
		else $per_page = 25;
		
		$limit = "$from,$per_page";


		/**
		 * get customer list
		 */
		
		$customer_list = $Customer->getClientList($customer_filter, $order_by, $per_page, $from);
		$customer_list_count = $Customer->getCustomerListCount($customer_filter);		
		
		if (is_array($customer_list) && count($customer_list) > 0) {
			
			/**
			 * Display pagination
			 */
			
			//$link = "/page/" . $_SESSION['active_pages'][0];
			
			$_Onxshop_Request = new Onxshop_Request("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$customer_list_count~");
			$this->tpl->assign('PAGINATION', $_Onxshop_Request->getContent());
			
			
			/**
			 * Display items
			 * 
			 */
		
			foreach ($customer_list as $i=>$customer) {
		
				$even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';
				$item['even_odd'] = $even_odd;
			
				$taxonomy = $Taxonomy->getRelationsToCustomer($customer['customer_id']);
				foreach ($taxonomy as $t) $customer['class'] .= "t$t ";
				
				$role_ids = $Customer->getRoleIds($customer['customer_id']);
				foreach ($role_ids as $r) $customer['class'] .= "role-$r ";
				
				$this->tpl->assign('ITEM', $customer);
				$this->tpl->parse('content.list.item');
			}
		
			$this->tpl->parse('content.list');
		} else {
			msg("No user found", 'error');
		}

		return true;
	}
	
	/**
	 * getOrderBy
	 */
	 
	public function getOrderBy() {
		
		if ($this->GET['customer-list-sort-by']) {
			$_SESSION['bo']['customer-list-sort-by'] = $this->GET['customer-list-sort-by'];
		}
		
		if ($this->GET['customer-list-sort-direction']) {
			$_SESSION['bo']['customer-list-sort-direction'] = $this->GET['customer-list-sort-direction'];
		}
		
		if ($_SESSION['bo']['customer-list-sort-by']) {
			$sort_by = $_SESSION['bo']['customer-list-sort-by'];
		} else {
			$sort_by = "customer_id";
		}
		
		if ($_SESSION['bo']['customer-list-sort-direction']) {
			$sort_direction = $_SESSION['bo']['customer-list-sort-direction'];
		} else {
			$sort_direction = "DESC";
		}
		
		switch ($sort_by) {
			case 'customer_id';
			default:
				$order_by = "client_customer.id $sort_direction";
		}
		
		return $order_by;
		
	}
}
