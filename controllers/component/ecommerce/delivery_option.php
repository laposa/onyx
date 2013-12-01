<?php
/**
 * Delivery Option
 *
 * Copyright (c) 2008-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/client/client_address.php');
require_once('models/ecommerce/ecommerce_basket.php');
require_once('models/ecommerce/ecommerce_delivery.php');
require_once('models/ecommerce/ecommerce_delivery_carrier.php');
require_once('models/ecommerce/ecommerce_delivery_carrier_zone.php');
require_once('models/ecommerce/ecommerce_order.php');

class Onxshop_Controller_Component_Ecommerce_Delivery_Option extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		$this->initModels();		 
		$options = $this->getInputOrDefaults();
		$address_detail = $this->getAddress();
		$zone_id = (int) $this->Delivery_Carrier_Zone->getZoneIdByCountry($address_detail['country']['id']);
		$carrier_list = $this->Delivery_Carrier->getList("zone_id = $zone_id AND publish = 1", "priority DESC, id ASC");

		$carrier_list = $this->processCarrierList($carrier_list, $address_detail['id'], $options);

		if (count($carrier_list) > 0) {

			foreach ($carrier_list as $i => $item) {
				
				if ($options['carrier_id']) {
					if ($item['id'] == $options['carrier_id']) $item['selected'] = "checked='checked'";
					else $item['selected'] = '';
				}				
				$this->tpl->assign("ITEM", $item);
				$this->tpl->parse('content.item');

			}

		} else {

			msg("Sorry, there is no delivery method available for your order value and weight.");
			$options['carrier_id'] = false;

		}
		
		$_SESSION['delivery_options'] = $options;

		return true;
	}
	
	protected function initModels()
	{
		$this->Address = new client_address();
		$this->Delivery = new ecommerce_delivery();
		$this->Delivery_Carrier = new ecommerce_delivery_carrier();
		$this->Delivery_Carrier_Zone = new ecommerce_delivery_carrier_zone();
		$this->Basket = new ecommerce_basket();
		$this->Order = new ecommerce_order();
	}

	protected function getInputOrDefaults()
	{
		if (is_array($_POST['delivery'])) return $_POST['delivery'];
		else if (is_array($_SESSION['delivery_options'])) return $_SESSION['delivery_options'];
		else return array('carrier_id' => $this->Delivery_carrier->conf['default_carrier_id']);
	}

	protected function getAddress()
	{
		return $this->Address->getDetail($_SESSION['client']['customer']['delivery_address_id']);
	}

	protected function processCarrierList($carrier_list, $delivery_address_id, &$delivery_options)
	{
		$result = array();

		$include_vat = $this->Order->isVatEligible($delivery_address_id, $_SESSION['client']['customer']['id']);
		$basket = $this->Basket->getFullDetail($_SESSION['basket']['id']);
		$this->Basket->calculateBasketSubTotals($basket, $include_vat);
		$code = $_SESSION['promotion_code'];
		$verify_code = true;
		$promotion_detail = $this->Basket->calculateBasketDiscount($basket, $code, $verify_code);

		$selected = false;
		$cheapest_carrier_price = 99999;
		$cheapest_carrier_id = null;

		foreach ($carrier_list as $carrier) {

			// is carrier available for given basket (e.g. order value and weight) ?
			$rate = $this->Delivery->calculateDelivery($basket, $carrier['id'], $delivery_address_id, $promotion_detail);

			if ($rate) {

				$carrier['calculated_delivery'] = $rate;
				$result[] = $carrier;
				if ($delivery_options['carrier_id'] == $carrier['id']) $selected = true;

				// save the cheapest delivery
				if ($rate['value'] < $cheapest_carrier_price) {
					$cheapest_carrier_price = $rate['value'];
					$cheapest_carrier_id = $carrier['id'];
				}
			}

		}

		// if no option is selected, auto select the cheapest one
		if (!$selected) $delivery_options['carrier_id'] = $cheapest_carrier_id;

		return $result;
	}

}
