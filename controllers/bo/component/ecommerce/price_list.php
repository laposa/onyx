<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Price_List extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		require_once('models/ecommerce/ecommerce_price.php');
		$Price = new ecommerce_price();
		
		
		$price_list = $Price->getPriceList($this->GET['product_variety_id']);

		foreach ($price_list as $item) {
			$item['usage'] = $Price->getAddedToBasketCount($item['id']);
			$this->tpl->assign('ITEM', $item);
			$this->tpl->parse('content.item');
		}

		return true;
	}
}
