<?php
/**
 * Order list filter
 *
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Order_List_Filter extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * initialize
		 */
		
		if (!isset($_SESSION['order-list-filter'])) $_SESSION['order-list-filter'] = array();
		
		/**
		 * Store submited data to the SESSION
		 */
		
		if (isset($_POST['order-list-filter'])) $_SESSION['order-list-filter'] = $_POST['order-list-filter'];
		if (!is_numeric($_SESSION['order-list-filter']['status']) && $_SESSION['order-list-filter']['status'] != 'all') $_SESSION['order-list-filter']['status'] = 1;
	
		/**
		 * for this controller only
		 */

		$order_list_filter = $_SESSION['order-list-filter'];
		//display all orders when looking for a customer
		if (is_numeric($this->GET['customer_id'])) $order_list_filter['status'] = 'all';
		
		/**
		 * create object
		 */

		require_once('models/ecommerce/ecommerce_order.php');
		$ObjOrder = new ecommerce_order();
		
		
		// status
		$status_types = $ObjOrder->conf['status'];
		
		$status_types['all'] = "All Orders"; 
			
		foreach ($status_types as $key=>$s) {
			$s1['id'] = $key;
			$s1['name'] = $s;
			if ($s1['id'] == $order_list_filter['status']) {
				$s1['selected'] = 'selected="selected"';
			} else {
				$s1['selected'] = '';
			}
			$this->tpl->assign('STATUS', $s1);
			$this->tpl->parse('content.status');
		}

		return true;
	}
}
