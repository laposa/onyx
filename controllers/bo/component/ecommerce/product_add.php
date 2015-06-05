<?php
/** 
 * Copyright (c) 2005-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Product_Add extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		
		$product_data = $_POST['product'];
		
		if ($_POST['save']) {
			if($id = $Product->insertProduct($product_data)) {
				msg("Product has been added.");
				onxshopGoTo("backoffice/products/$id/variety_add");
			} else {
				msg("Adding of Product Failed.", 'error');
			}
		}
		$this->tpl->assign('PRODUCT', $product_data);

		return true;
	}
}
