<?php
/** 
 * Copyright (c) 2005-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/basket.php');
require_once('models/ecommerce/ecommerce_delivery.php');
require_once('models/ecommerce/ecommerce_order.php');

class Onxshop_Controller_Component_Ecommerce_Basket_Detail extends Onxshop_Controller_Component_Ecommerce_Basket {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * show only in default currency
		 */
		 
		setlocale(LC_MONETARY, $GLOBALS['onxshop_conf']['global']['locale']);
		
		parent::mainAction();

		setlocale(LC_MONETARY, LOCALE);
		return true;
	}

	/**
	 * set vat flag according to delivery address
	 */
	protected function setVatFlag($customer_id)
	{
		$Order = new ecommerce_order();
		$this->include_vat = $Order->isVatEligible($this->delivery_address_id, $customer_id);
	}

	/**
	 * customer calculations
	 */
	protected function processBasketCalculations(&$basket)
	{
		$this->prepareAddresses();
		$this->setVatFlag($basket['customer_id']);

		$this->Basket->calculateBasketSubTotals($basket, $this->include_vat);
		$promotion_data = $this->Basket->calculateBasketDiscount($basket, $this->getPromotionCode());
		$this->calculateDelivery($basket, $promotion_data);
		$this->Basket->calculateBasketTotals($basket);
	}


	/**
	 * prepare addresses
	 */
	protected function prepareAddresses()
	{
		//prepare shipping address
		if (is_numeric($this->GET['delivery_address_id'])) $this->delivery_address_id = $this->GET['delivery_address_id'];
		else if (is_numeric($_SESSION['client']['customer']['delivery_address_id'])) $this->delivery_address_id = $_SESSION['client']['customer']['delivery_address_id'];
		else msg('Unknown delivery_address_id', 'error');

		//prepare delivery options
		if (is_array($this->GET['delivery_options'])) $this->delivery_options = $this->GET['delivery_options'];
		else if (is_array($_SESSION['delivery_options'])) $this->delivery_options = $_SESSION['delivery_options'];
		else $this->delivery_options = false;
	}

	/**
	 * calculate delivery
	 * @param  [type] $basket [description]
	 * @return [type]         [description]
	 */
	protected function calculateDelivery(&$basket, $promotion_data)
	{
		$Delivery = new ecommerce_delivery();

		// use delivery from the ecommerce_delivery table (contains right values if a promotion code have been applied before)
		if (is_numeric($order_id = $this->GET['order_id']))
			$basket['delivery'] = $Delivery->getDeliveryByOrderId($order_id);
		else 
			$basket['delivery'] = $Delivery->calculateDelivery($basket, $this->delivery_address_id, $this->delivery_options, $promotion_data);
	}	

	protected function getPromotionCode()
	{
		if (is_numeric($order_id = $this->GET['order_id'])) {

			require_once('models/ecommerce/ecommerce_promotion.php');
			$Promotion = new ecommerce_promotion();
			return $Promotion->getPromotionCodeForOrder($order_id);

		} else {

			return $_SESSION['promotion_code'];

		}
	}
}
