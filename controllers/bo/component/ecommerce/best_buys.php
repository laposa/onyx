<?php
/** 
 * Copyright (c) 2008-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/ecommerce/best_buys.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Best_Buys extends Onxshop_Controller_Component_Ecommerce_Best_Buys {

	
	/**
	 * render list
	 */
	 
	public function renderList($product_list, $controller = 'product_list_shelf') {
	
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
