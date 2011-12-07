<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/basket.php');

class Onxshop_Controller_Component_Ecommerce_Basket_Detail extends Onxshop_Controller_Component_Ecommerce_Basket {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * show only in default currency
		 */
		 
		setlocale(LC_MONETARY, $GLOBALS['onxshop_conf']['global']['locale']);
		
		/**
		 * create basket object
		 */
		 
		//$this->Basket = $this->initBasket();
		
		/**
		 * display basket
		 */
		 
		//$this->displayBasket($this->GET['id']);

		$this->displayBasketCustom();
		
		setlocale(LC_MONETARY, LOCALE);
		return true;
	}
	
	/**
	 * display basket old method
	 */
	
	function displayBasketCustom() {
	
		require_once('models/ecommerce/ecommerce_basket.php');
		$Basket = new ecommerce_basket();
		$Basket->setCacheable(false);
		
		/**
		 * assign VAT note
		 */
		 
		$this->assignVATNote();
		
		/**
		 * Get basket detail
		 */
		
		
		$basket_detail = $Basket->getDetail($this->GET['id']);
		
		if (count($basket_detail['content']['items']) > 0) {
			
			/**
			 * display items
			 */
			 
			foreach ($basket_detail['content']['items'] as $item) {
			
				//product other_data options
				if (is_array($item['other_data']) && count($item['other_data']) > 0) $item['other_data'] = implode(",", $item['other_data']);
				else $item['other_data'] = '';

			
				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.basket.item');
			}
			
			//prepare shipping address
			if (is_numeric($this->GET['delivery_address_id'])) $delivery_address_id = $this->GET['delivery_address_id'];
			else if (is_numeric($_SESSION['client']['customer']['delivery_address_id'])) $delivery_address_id = $_SESSION['client']['customer']['delivery_address_id'];
			else msg('Unknown delivery_address_id', 'error');
		
			//prepare delivery options
			if (is_array($this->GET['delivery_options'])) $delivery_options = $this->GET['delivery_options'];
			else if (is_array($_SESSION['delivery_options'])) $delivery_options = $_SESSION['delivery_options'];
			else $delivery_options = false;
			
			/**
			 * calculate delivery
			 */
			 
			require_once('models/ecommerce/ecommerce_delivery.php');
			$Delivery = new ecommerce_delivery();

			//overwrite delivery data if this is a submitted order
			if (is_numeric($order_id = $this->GET['order_id'])) {
				//from ecommerce_promotion_code table
				//use delivery from the ecommerce_delivery table (contains right values if a promotion code have been applied before)
				$delivery_data = $Delivery->getDeliveryByOrderId($order_id);
			} else if ($_SESSION['promotion_code']) {
				//with a promotional code (it's a live basket, not submitted as an order)
				require_once('models/ecommerce/ecommerce_promotion.php');
				$Promotion = new ecommerce_promotion();
				$customer_id = $basket_detail['customer_id'];
				$promotion_data = $Promotion->checkCodeBeforeApply($_SESSION['promotion_code'], $customer_id);
				$delivery_data = $Delivery->calculateDelivery($basket_detail['content'], $delivery_address_id, $delivery_options, $promotion_data);
			} else {
				$delivery_data = $Delivery->calculateDelivery($basket_detail['content'], $delivery_address_id, $delivery_options);
			}


			/**
			 * update total with delivery
			 */
			
			$basket_detail['content']['delivery'] = $delivery_data;
			$basket_detail['content']['total'] = $basket_detail['content']['total_after_discount'] + $basket_detail['content']['delivery']['value'];
			
			//display VAT
			if ($delivery_data['vat_rate'] > 0) {
				$vat['rate'] = $delivery_data['vat_rate'];
				$vat['value'] = $basket_detail['content']['total_vat'] + $delivery_data['vat'];
				$this->tpl->assign('VAT', $vat);
				$this->tpl->parse('content.basket.vat');
			}
			
			//print_r($basket_detail);
			$this->tpl->assign('BASKET', $basket_detail['content']);
					
			/**
		 	 * find if any fixed discount was given, ie. promotion code applies
		 	 */

			if ($basket_detail['content']['discount_net'] > 0) {
				$this->tpl->parse('content.basket.discount');
			}
			
			$this->tpl->parse('content.basket');
		} else {
			$this->tpl->parse('content.empty');
		}

	}
}
