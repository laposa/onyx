<?php
/** 
 * Copyright (c) 2007-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Ecommerce_Product_Related_to_customer extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * create objects
		 */
		 
		require_once('models/ecommerce/ecommerce_product.php');
		require_once('models/ecommerce/ecommerce_product_to_product.php');
		
		$Product = new ecommerce_product();
		$PtP = new ecommerce_product_to_product();
		
		/**
		 * Set Variables
		 */
		 
		if ($this->GET['type'] == 'worst') $order = 'ASC';
		else $order = 'DESC';
		
		if (is_numeric($this->GET['limit'])) $limit = $this->GET['limit'];
		else $limit = 5;
		
		if (is_numeric($this->GET['customer_id'])) $customer_id = $this->GET['customer_id'];
		else if ($this->GET['customer_id'] == 'session' && $_SESSION['client']['customer']['id'] > 0) $customer_id = $_SESSION['client']['customer']['id'];
		else $customer_id = false;
		
		/**
		 * type
		 */
		 
		switch ($this->GET['type']) {
			case 'static':
				$type = 'static';
			break;
			case 'dynamic':
			default:
				$type = 'dynamic';
			break;
		}
		
		
		/**
		 * Get product_list
		 */
		 
		$product_list = $Product->getMostPopularProducts($order, $limit, $customer_id);
		
		$related = array();
		
		foreach ($product_list as $item) {
			$related_actual = $PtP->getRelatedProduct($item['product_id'], 1, $type);
		
			$related = array_merge($related, $related_actual);
		}
		
		/**
		 * detect controller (template) for product list
		 */

		switch ($this->GET['template']) {
			case 'scroll':
				$controller = 'product_list_scroll';
				break;
			case '4col':
				$controller = 'product_list_4columns';
				break;
			case '3col':
				$controller = 'product_list_3columns';
				break;
			case '2col':
				$controller = 'product_list_2columns';
				break;
			case '1col':
			default:
				$controller = 'product_list_shelf';
				break;
		}
		
		/**
		 * Pass product_id_list to product_list controller
		 */
		
		if (is_array($related) && count($related) > 0) {
		
			/**
			 * prepare HTTP query for product_list component
		 	 */
		
			$related_list['product_id_list'] = $related;
			$query = http_build_query($related_list, '', ':');
		
			/**
			 * call controller
			 */
			 
			$_nSite = new nSite("component/ecommerce/$controller~{$query}~");
			$this->tpl->assign('ITEMS', $_nSite->getContent());
			$this->tpl->parse('content.product_related');
		}

		return true;
	}
}
