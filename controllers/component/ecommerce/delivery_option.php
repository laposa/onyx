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
		
		$carrier_list = $Delivery_carrier->listing("publish = 1", "priority DESC, id ASC");
		
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
}
