<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Product_Edit extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * initialize
		 */
		 
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		
		/**
		 * save
		 */
		 
		if ($_POST['save']) {
		
			/**
			 * set values
			 */
			 
			if (!isset($_POST['product']['publish'])) $_POST['product']['publish'] = 0;
			
			$_POST['product']['modified'] = date('c');
			
			/**
			 * handle other_data
			 */
			
			$_POST['product']['other_data'] = serialize($_POST['product']['other_data']);
			
			/**
			 * update product
			 */
			 
			if($id = $Product->update($_POST['product'])) {
			
				msg("Product ID=$id updated");
			
				/**
				 * update node info (if exists)
				 */
				 
				$product_homepage = $Product->getProductHomepage($_POST['product']['id']);
			
				if (is_array($product_homepage) && count($product_homepage) > 0) {
					$product_homepage['publish'] = $_POST['product']['publish'];
					
					require_once('models/common/common_node.php');
					$Node = new common_node();
					
					$Node->nodeUpdate($product_homepage);
					
				}
				
				
				
				
				/**
				 * forward to product list main page and exit
				 */	
				
				onxshopGoTo("/backoffice/products");
				
				return true;
			}
		}
		
		/**
		 * product detail
		 */
		 
		$product = $Product->detail($this->GET['id']);
		$product['publish'] = ($product['publish'] == 1) ? 'checked="checked" ' : '';
		$this->tpl->assign('PRODUCT', $product);

		return true;
	}
}	
			
