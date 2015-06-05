<?php
/** 
 * Copyright (c) 2010-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */


class Onxshop_Controller_Bo_Component_Ecommerce_Product_List_By_Customer extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * create object
		 */
		 
		require_once('models/client/client_customer.php');
		$Customer = new client_customer();
		
		/**
		 * Set Variables
		 */
		 
		if ($this->GET['type'] == 'worst') $order = 'ASC';
		else $order = 'DESC';
		
		/**
		 * number of items limit
		 */
		
		if (is_numeric($this->GET['limit'])) $limit = $this->GET['limit'];
		else $limit = false;
		
		/**
		 * period limit in days
		 */
		 
		if (is_numeric($this->GET['period_limit'])) $period_limit = $this->GET['period_limit'];
		else $period_limit = 7;
		
		/**
		 * customer limit
		 */
		 
		if (is_numeric($this->GET['customer_id'])) $customer_id = $this->GET['customer_id'];
		else if ($this->GET['customer_id'] == 'session') {
			if ($_SESSION['client']['customer']['id'] > 0) {
				$customer_id = $_SESSION['client']['customer']['id'];
			} else {
				msg("You are not logged in as a customer, displaying normal best buys");
				$customer_id = false;
			}
		} else {
			$customer_id = false;
		}
		
		



		 
		/**
		 * Get product_list
		 */
		 
		$product_list = $Customer->getProductsByCustomer($order, $limit, $customer_id, $period_limit);
		
		/**
		 * if product sales in last 7 was empty, recalculate with no period limit
		 */
		  
		if (count($product_list) == 0) {
			$period_limit = 0;
			$product_list = $Customer->getProductsByCustomer($order, $limit, $customer_id, $period_limit);
		}
		
		/**
		 * Pass product_id_list to product_list controller
		 */
			
		$this->renderList($product_list);

		return true;
	}
	
	/**
	 * render list
	 */
	 
	public function renderList($product_list) {
	
		if (is_array($product_list)) {
			
			require_once('models/ecommerce/ecommerce_product.php');
		
			$Product = new ecommerce_product();
		
			foreach ($product_list as $i=>$item) {
				$current = $Product->findProductInNode($item['product_id']);
				$product_list[$i]['node_id'] = $current[0]['id'];
			
			}
			
			/**
			 * Display items
			 */
			
			foreach ($product_list as $item) {
				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.item');
			}

			if (count($product_list) == 0) $this->tpl->parse('content.empty');
		}
	}

}
