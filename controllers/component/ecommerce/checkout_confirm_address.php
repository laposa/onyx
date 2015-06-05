<?php
/** 
 * Copyright (c) 2010-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Checkout_Confirm_Address extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * include node configuration
		 */
		require_once('models/common/common_node.php');
		$node_conf = common_node::initConfiguration();
		
		/**
		 * client
		 */
		require_once('models/client/client_address.php');
		$Address = new client_address();
		
		if (is_numeric($this->GET['invoices_address_id'])) $invoices_address_id = $this->GET['invoices_address_id'];
		if (is_numeric($this->GET['delivery_address_id'])) $delivery_address_id = $this->GET['delivery_address_id'];
		
		// is guest checkout required?
		$guest_checkout = $_SESSION['client']['customer']['guest'];

		// address edit link
		if ($guest_checkout) $this->tpl->assign('UPDATE_PAGE_ID', $node_conf['id_map-guest_registration']);
		else $this->tpl->assign('UPDATE_PAGE_ID', $node_conf['id_map-checkout_delivery_options']);

		//if we have not address_ids, we'll use session data
		if (!is_numeric($invoices_address_id) && !is_numeric($delivery_address_id)) {
			$invoices_address_id = $_SESSION['client']['customer']['invoices_address_id'];
			$delivery_address_id = $_SESSION['client']['customer']['delivery_address_id'];
		}

		if (is_numeric($invoices_address_id)) {
			$invoices = $Address->getDetail($invoices_address_id);
		} else if ($guest_checkout) {
			$invoices = $_SESSION['client']['address']['invoices'];
			$invoices['country']['name'] = $this->getCountryName($invoices['country_id']);
		} else {
			$invoices = false;
		}
		
		if (is_numeric($delivery_address_id)) {
			$delivery = $Address->getDetail($delivery_address_id);
		} else if ($guest_checkout) {
			$delivery = $_SESSION['client']['address']['delivery'];
			$delivery['country']['name'] = $this->getCountryName($delivery['country_id']);
		} else {
			$delivery = false;
		}
		
		$addr['invoices'] = $invoices;
		$addr['delivery'] = $delivery;
		$this->tpl->assign('ADDRESS', $addr);
		
		if (is_array($addr['invoices'])) {
			if ($addr['invoices']['line_2'] != '') $this->tpl->parse('content.invoices.line_2');
			if ($addr['invoices']['line_3'] != '') $this->tpl->parse('content.invoices.line_3');
			if ($this->GET['hide_button'] == 0) $this->tpl->parse('content.invoices.button');
			$this->tpl->parse('content.invoices');
		}
		
		if (is_array($addr['delivery'])) {
			if ($addr['delivery']['line_2'] != '') $this->tpl->parse('content.delivery.line_2');
			if ($addr['delivery']['line_3'] != '') $this->tpl->parse('content.delivery.line_3');
			if ($this->GET['hide_button'] == 0) $this->tpl->parse('content.delivery.button');
			$this->tpl->parse('content.delivery');
		}

		return true;
	}

	protected function getCountryName($country_id)
	{
		require_once('models/international/international_country.php');
		$Country = new international_country();
		$country = $Country->detail($country_id);
		return $country['name'];
	}
}
