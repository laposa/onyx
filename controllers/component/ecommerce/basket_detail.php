<?php
/** 
 * Copyright (c) 2005-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/basket.php');
require_once('models/ecommerce/ecommerce_delivery.php');
require_once('models/ecommerce/ecommerce_order.php');
require_once('models/ecommerce/ecommerce_promotion.php');

class Onxshop_Controller_Component_Ecommerce_Basket_Detail extends Onxshop_Controller_Component_Ecommerce_Basket {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		// show only in default currency
		setlocale(LC_MONETARY, $GLOBALS['onxshop_conf']['global']['locale']);

		$this->initModels();

		$this->basket_id = $this->getBasketId();
		$this->customer_id = (int) $_SESSION['client']['customer']['id'];

		$this->checkForPreviousBasket();

		if ($this->basket_id > 0) {

			$basket = $this->Basket->getFullDetail($this->basket_id);

			if (count($basket['items']) > 0) {

				$this->processBasketCalculations($basket);
				$this->displayBasket($basket);

				return true;

			}

		} 

		$this->displayEmptyBasket();

		setlocale(LC_MONETARY, LOCALE);
		return true;
	}

	/**
	 * set vat flag according to delivery address
	 */
	 
	protected function setVatFlag($customer_id)
	{
		$Order = new ecommerce_order();

		if ($this->guest_customer) {

			$this->include_vat = $Order->isVatEligibleByCountry($this->delivery_country);

		} else {

			$this->include_vat = $Order->isVatEligible($this->delivery_address_id, $customer_id);

		}
	}

	/**
	 * customer calculations
	 */
	 
	protected function processBasketCalculations(&$basket)
	{
		$this->prepareAddresses();
		$this->setVatFlag($basket['customer_id']);

		$this->Basket->calculateBasketSubTotals($basket, $this->include_vat);
		$this->calculateDiscountAndDelivery($basket);

		$this->Basket->calculateBasketTotals($basket);
	}
	
	/**
	 * calculateDiscountAndDelivery
	 */

	protected function calculateDiscountAndDelivery(&$basket)
	{
		$Delivery = new ecommerce_delivery();

		// order exists?
		if ($this->orderFinished($basket['id'], $this->GET['order_id'])) {

			$Promotion = new ecommerce_promotion();
			$Promotion->setCacheable(false);

			// get data from database
			$code = $Promotion->getPromotionCodeForOrder($this->GET['order_id']);
			$verify_code = false;
			$this->Basket->calculateBasketDiscount($basket, $code, $verify_code);
			$basket['delivery'] = $Delivery->getDeliveryByOrderId($this->GET['order_id']);

		} else {

			// calculate data
			$code = $_SESSION['promotion_code'];
			$verify_code = true;
			$promotion_detail = $this->Basket->calculateBasketDiscount($basket, $code, $verify_code);
			$this->Basket->saveDiscount($basket);

			if ($this->guest_customer) {
				$basket['delivery'] = $Delivery->calculateDeliveryForCountry(
					$basket, 
					$this->delivery_options['carrier_id'], 
					$this->delivery_country, 
					$promotion_detail
				);
			} else {
				$basket['delivery'] = $Delivery->calculateDelivery(
					$basket, 
					$this->delivery_options['carrier_id'], 
					$this->delivery_address_id, 
					$promotion_detail
				);
			}

			// this only applies when using wizard checkout
			if ($basket['delivery'] == false) $this->redirectToDeliveryOptionsPage();

		}

	}

	/**
	 * redirectToDeliveryOptionsPage
	 */
	 
	protected function redirectToDeliveryOptionsPage()
	{
		require_once('models/common/common_node.php');
		$node_conf = common_node::initConfiguration();

		msg("Sorry, selected delivery method cannot be used. Please choose a different one.");
		// forward only if there is a separate checkout delivery options page
		if ($node_conf['id_map-checkout_delivery_options'] != $_SESSION['active_pages'][0]) onxshopGoTo("page/{$node_conf['id_map-checkout_delivery_options']}");
	}

	/**
	 * check if given order is finished and related to given basket
	 */
	 
	protected function orderFinished($basket_id, $order_id)
	{
		if (!is_numeric($order_id)) return false;
		$Order = new ecommerce_order();
		$order = $Order->getDetail($order_id);
		return ($order['basket_id'] == $basket_id);
	}

	/**
	 * prepare addresses
	 */
	 
	protected function prepareAddresses()
	{
		$this->guest_customer = $_SESSION['client']['customer']['guest'];
		$this->delivery_country = $_SESSION['client']['address']['delivery']['country_id'];

		//prepare shipping address
		if (is_numeric($this->GET['delivery_address_id'])) $this->delivery_address_id = $this->GET['delivery_address_id'];
		else if (is_numeric($_SESSION['client']['customer']['delivery_address_id'])) $this->delivery_address_id = $_SESSION['client']['customer']['delivery_address_id'];
		else if (!$this->guest_customer) msg('Unknown delivery_address_id', 'error');

		//prepare delivery options
		if (is_array($this->GET['delivery_options'])) $this->delivery_options = $this->GET['delivery_options'];
		else if (is_array($_SESSION['delivery_options'])) $this->delivery_options = $_SESSION['delivery_options'];
		else $this->delivery_options = false;
	}

}
