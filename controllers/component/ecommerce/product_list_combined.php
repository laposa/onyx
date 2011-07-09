<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/product_list.php');

class Onxshop_Controller_Component_Ecommerce_Product_List_Combined extends Onxshop_Controller_Component_Ecommerce_Product_List {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		return $this->processProductList();
	
	}
	
	/**
	 * process items
	 */
	 
	function processItems($product_list, $image_width, $from, $per_page, $divide_after = 3) {
		$this->_displayItems($product_list, $image_width, $from, $per_page, 1, 'itemtop');
		//$this->_displayItems($product_list, $image_width, 6, 100, 3, 'item');
		return true;
	}

}
