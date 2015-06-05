<?php
/** 
 * Copyright (c) 2006-2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Price_List_Ajax extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		require_once('models/ecommerce/ecommerce_price.php');
		$Price = new ecommerce_price();
		
		$price_list = $Price->getPriceList($this->GET['product_variety_id']);
		$this->tpl->assign('CONTENT', json_encode($price_list));

		return true;
	}
}
