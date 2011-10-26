<?php
/**
 * Delivery Option
 *
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Delivery_Option extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * initialize
		 */
		 
		require_once('models/ecommerce/ecommerce_delivery_carrier.php');
		$Delivery_carrier = new ecommerce_delivery_carrier();
	
		/**
		 * Input data
		 */
		
		if (is_array($_POST['delivery'])) {
			$options = $_POST['delivery'];
		} else if (is_array($_SESSION['delivery_options'])) {
			$options = $_SESSION['delivery_options'];
		} else {
			//TODO: find default carrier from ecommerce_delivery_carrier table
			//royal mail
			$options['carrier_id'] = $Delivery_carrier->conf['default_carrier_id'];
		}
		
		
		/**
		 * Initialize object
		 */
		require_once('models/ecommerce/ecommerce_delivery.php');
		$Delivery = new ecommerce_delivery();
		
		/**
		 * Get alowed delivery options
		 */
		
		require_once('models/client/client_address.php');
		
		$Address = new client_address();
		$address_detail = $Address->getDetail($_SESSION['client']['customer']['delivery_address_id']);
		
		/**
		 * get available options
		 */
		
		$carrier_list = $Delivery_carrier->getList("publish = 1", "priority DESC, id ASC");
		
		foreach ($carrier_list as $carrier) {
				if ($carrier['limit_list_countries']) {
					if ($carrier['limit_list_countries'] == $address_detail['country']['id']) $delivery_option_type[] = $carrier;
				} else {
					$delivery_option_type[] = $carrier;
				}
		}
		
		
		/**
		 * check if isn't choosen unsuported delivery method
		 */
		 
		if (is_numeric($options['carrier_id'])) {
			$selected_carrier_detail = $Delivery_carrier->detail($options['carrier_id']);
			if ($selected_carrier_detail['limit_list_countries']) {
				if ($carrier['limit_list_countries'] != $address_detail['country']['id']) {
					msg("Unsupported delivery method for {$address_detail['country']['name']}. Changed to {$delivery_option_type[0]['title']}");
					$options['carrier_id'] = $delivery_option_type[0]['id'];
				}
			}
		}
		
		
		/**
		 * add delivery price information
		 */
		
		foreach ($delivery_option_type as $k=>$item) {
			
			$delivery_option_type[$k]['calculated_delivery'] = $this->calculateDeliveryForCarrierId($item['id']);
		
		}
		
		/**
		 * force to delivery method which is last in the list and is free (should be best best available)
		 */
		
		foreach ($delivery_option_type as $k=>$item) {
			
			if ($item['calculated_delivery']['value'] == 0) $options['carrier_id'] = $item['id'];
		
		}
		
		
		/**
		 * Display
		 */
		
		foreach ($delivery_option_type as $item) {
			
			if ($item['id'] == $options['carrier_id']) $item['selected'] = "checked='checked'";
			else $item['selected'] = '';
			
			$this->tpl->assign("ITEM", $item);
			$this->tpl->parse('content.item');
		}
		
		
		/**
		 * Save in session
		 */
		
		$_SESSION['delivery_options'] = $options;

		return true;
	}
	
	/**
	 * calculate delivery
	 */
	 
	public function calculateDeliveryForCarrierId($carrier_id) {
		
		$basket_id = $_SESSION['basket']['id'];
		$delivery_address_id = $_SESSION['client']['customer']['delivery_address_id'];
		$delivery_options = array('carrier_id'=>$carrier_id);
		$promotion_code = $_SESSION['promotion_code'];
		
		/**
		 * prepare for delivery pre-calculation
		 */
		 
		require_once('models/ecommerce/ecommerce_basket.php');
		$Basket = new ecommerce_basket();
		
		$delivery = $Basket->calculateDelivery($basket_id, $delivery_address_id, $delivery_options, $promotion_code);
		
		return $delivery;
	}
}
