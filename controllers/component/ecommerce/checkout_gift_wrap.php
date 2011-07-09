<?php
/** 
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * TODO: fix temp variable for jing gift wrap product id = 238, variety id = 380
 */

require_once('controllers/component/ecommerce/checkout_gift.php');

class Onxshop_Controller_Component_Ecommerce_Checkout_Gift_Wrap extends Onxshop_Controller_Component_Ecommerce_Checkout_Gift {

	/**
	 * public action
	 */
	 
	public function mainAction() {
	
		/**
		 * get gift wrap product detail
		 */
		 
	 	$gift_wrap_products = $this->getGiftWrapProductDetail();
	 	
	 	
	 	foreach ($gift_wrap_products as $item) {
	 		
	 		$this->tpl->assign('ITEM', $item);
	 	
			/**
			 * check if gift wrap is in the basket
			 */
			 
			$gift_selected= $this->checkGiftWrapSelected($item['variety_id']);
			
			/**
			 * display checked gift wrap
			 */
	
			if ($gift_selected) {
				$this->tpl->assign("CHECKED_gift_wrap", "checked='checked'");
			} else {
				$this->tpl->assign("CHECKED_gift_wrap", "");
			}
			
			$this->tpl->parse('content.item');
		}

		return true;
	}
	
	/**
	 * get gift wrap product detail
	 */
	
	public function getGiftWrapProductDetail() {
		
		$detail['product_id'] = 238;
		$detail['variety_id'] = 380;
		$detail['price_gross'] = 2.10;
		$detail['image'] = 'var/files/products/gift_wrap.png';
		$detail['title'] = 'JING Gift Wrap';
		
		$gift_wrap_products[] = $detail;
		
		$detail['product_id'] = 238;
		$detail['variety_id'] = 686;
		$detail['price_gross'] = 2.10;
		$detail['image'] = 'var/files/Packs/11065KH_Giftbox1.jpg';
		$detail['title'] = 'JING Gift Bag';
		
		$gift_wrap_products[] = $detail;
		
		return $gift_wrap_products;
	}
	
		
	/**
	 * check if gift wrap is in basket
	 */
	 
	public function checkGiftWrapSelected($variety_id) {
		
		require_once('models/ecommerce/ecommerce_basket.php');
		$Basket = new ecommerce_basket();
		$Basket->setCacheable(false);
		
		$variety_id_list = $Basket->getContentItemsVarietyIdList($_SESSION['basket']['id']);
		
		if (in_array($variety_id, $variety_id_list)) return true;
		else return false;
	}
	
}
