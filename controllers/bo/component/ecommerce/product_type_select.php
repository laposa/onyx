<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Product_Type_Select extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * input
		 */
		 
		if (is_numeric($this->GET['id'])) $product_id = $this->GET['product_id'];
		else $product_id = false;
		
		/**
		 * initialize
		 */
		 
		require_once('models/ecommerce/ecommerce_product.php');
		require_once('models/ecommerce/ecommerce_product_type.php');
		$Product = new ecommerce_product();
		$ProductType = new ecommerce_product_type();
		
		/**
		 * get product detail if requested
		 */
		 
		if (is_numeric($product_id)) $product = $Product->detail($product_id);
		
		/**
		 * prepare product type id (either for requested product or default one)
		 */
		 
		if (is_numeric($product['product_type_id'])) $product_type_id = $product['product_type_id'];
		else $product_type_id = $ProductType->conf['default_id'];
		
		/**
		 * listing published items
		 */
		 
		$types = $ProductType->listing("publish = 1");
		
		foreach ($types as $type) {
			$this->tpl->assign('TYPE', $type);
			if ($type['id'] == $product_type_id) {
				$this->tpl->assign('SELECTED', 'selected="selected"');
			} else {
				$this->tpl->assign('SELECTED', '');
			}
			$this->tpl->parse('content.type');
		}

		return true;
	}
}
