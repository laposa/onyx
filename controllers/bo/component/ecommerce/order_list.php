<?php
/**
 * Order list controller
 *
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Order_List extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/*$order = $this->GET['order'];
		$search = trim($_POST['search']);
		$customer_id = $this->GET['customer_id'];
		$status = $this->GET['status'];*/
			
		if (is_array($_SESSION['order-list-filter'])) $order_list_filter = $_SESSION['order-list-filter'];
		else $order_list_filter = array();
		
		if (is_numeric($this->GET['customer_id'])) {
			$customer_id = $this->GET['customer_id'];
			//display all orders when looking for a customer
			$order_list_filter = array();
			$order_list_filter['status'] = 'all';
		} else {
			$customer_id = NULL;
		}

		if (is_numeric($this->GET['status'])) $order_list_filter['status'] = $this->GET['status'];
		
		
		/**
		 * if query is numeric, go strait to order detail
		 */
		
		if (is_numeric($_POST['order-list-filter']['query'])) onxshopGoTo("/backoffice/orders/{$_POST['order-list-filter']['query']}/detail");

		/**
		 * Initialize pagination variables
		 */
		
		if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
		else $from = 0;
		if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
		else $per_page = 25;
		
		
		$limit = "$from,$per_page";

		/**
		 * Initialize order object
		 */
		require_once('models/ecommerce/ecommerce_order.php');
		
		$Order = new ecommerce_order();
		
		/**
		 * Get order list
		 */
		$order_list = $Order->getOrderList($customer_id, $order_list_filter, $per_page, $from);
		$count = $Order->getOrderListCount($customer_id, $order_list_filter);

		if ($count > 0) {
						
			/**
			 * Display pagination
			 */
			
			$_Onxshop_Request = new Onxshop_Request("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count~");
			$this->tpl->assign('PAGINATION', $_Onxshop_Request->getContent());
			
			/**
			 * Display items
			 * Implemented pagination
			 */
		
			foreach ($order_list as $item) {
				
				$even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';
				$item['even_odd'] = $even_odd;
			
				$item['order_created'] = strftime('%c', strtotime($item['order_created']));
				$item['last_activity'] = strftime('%c', strtotime($item['last_activity']));
				if (!is_numeric($item['goods_net'])) $item['goods_net'] = 0;

				// display payment due (for unpaid orders only)
				if ($item['order_status'] == 0 && isset($item['other_data']['payment_due']))
					$item['payment_due'] = $item['other_data']['payment_due'];

				$item['status'] = $Order->getStatusTitle($item['order_status']);
		
				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.list.item');

			}

			$this->tpl->parse('content.list');

		} else {

			$this->tpl->parse('content.empty');

		}

		return true;
	}
}		
