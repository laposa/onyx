<?php
/**
 * Copyright (c) 2008-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Ecommerce_Recently_Viewed_Products extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		//do nothing if basket is not initialized
		if (!is_numeric($_SESSION['basket']['id'])) return true;
		
		/**
		 * create object
		 */
		 
		require_once('models/ecommerce/ecommerce_basket.php');
		$Basket = new ecommerce_basket();
		
		$basket_content_ids = $Basket->getContentItemsProductIdList($_SESSION['basket']['id']);
		$viewed_list_ids = array();
		
		$Node = new common_node();
		$viewed_products = array();
		
		/**
		 * go through history
		 */
		
		foreach ($_SESSION['history'] as $item) {
			if (is_numeric($item['node_id'])) {
				$node_data = $Node->nodeDetail($item['node_id']);
				
				if ($node_data['node_controller'] == 'product') {
					if (!in_array($node_data['content'], $basket_content_ids) && !in_array($node_data['content'], $viewed_list_ids) && $_SESSION['active_pages'][0] != $node_data['id']) {
						$viewed_products[] = $node_data['content'];
					}
				}
			}
		}
		
		/**
		 * Pass product_id_list to product_list controller
		 */
		
		if (is_array($viewed_products) && count($viewed_products) > 0) {
			
			/**
			 * prepare HTTP query for product_list component
			 */
		
			$viewed_list['product_id_list'] = $viewed_products;
			$query = http_build_query($viewed_list, '', ':');
		
			/**
			 * detect controller for product list
			 */
	
			switch ($this->GET['template']) {
				case 'scroll':
					$controller = 'product_list_scroll';
					break;
				case '3col':
					$controller = 'product_list_3columns';
					break;
				case '2col':
					$controller = 'product_list_2columns';
					break;
				case '1col':
				default:
					$controller = 'product_list';
					break;
			}
			
			/**
			 * call controller
			 */
			 
			$_Onxshop_Request = new Onxshop_Request("component/ecommerce/$controller~{$query}:image_width={$this->GET['image_width']}~");
			$this->tpl->assign('ITEMS', $_Onxshop_Request->getContent());
			$this->tpl->parse('content.recently_viewed');
		}

		return true;
	}
}
