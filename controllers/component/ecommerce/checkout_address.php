<?php
/** 
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/checkout.php');

class Onxshop_Controller_Component_Ecommerce_Checkout_Address extends Onxshop_Controller_Component_Ecommerce_Checkout {

	/**
	 * public action
	 */
	 
	public function mainAction() {
	
		/**
		 * initialize
		 */
	
		$this->initialize();
		
		
		/**
		 * add address
		 */
		 
		if ($_POST['node_id'] == $this->GET['node_id'] && $_POST['add_address']) {
		
			$address_id = $this->addAddress();
			
		}
		
		/**
		 * select address
		 */
		
		if ($_POST['node_id'] == $this->GET['node_id'] && (is_numeric($_POST['selected_address_id']) || is_numeric($address_id))) {
			
			if (!is_numeric($address_id)) $address_id =  $_POST['selected_address_id'];
			
			$this->selectAddress($address_id, $this->GET['type']);
			
		}
		
		/**
		 * remove address
		 */
		 
		if ($_POST['node_id'] == $this->GET['node_id'] && is_numeric($_POST['remove_address'])) {
		
			$this->removeAddress($_POST['remove_address']);
			
		}

		/**
		 * edit address
		 */
		 
		if ($_POST['node_id'] == $this->GET['node_id'] && is_numeric($_POST['edit_address'])) {
		
			$this->editAddress($_POST);
			
		}
		
		/**
		 * address list
		 */
		
		$this->displayAddressList($this->GET['type']);
				
		/**
		 * country list
		 */
		
		$this->displayCountryList();
		
		
		/**
		 * assign to template
		 */
		
		$this->tpl->assign('client', $_POST['client']);
		
		/**
		 * display virtual product option
		 */
		 
		if ($this->GET['type'] == 'delivery') {
			if ($this->isBasketVirtualProductOnly()) $this->tpl->parse('content.virtual_product');
		}
		
		return true;
	}
	
	
	/**
	 * initialize
	 */
	 
	public function initialize() {
	
		require_once('models/client/client_customer.php');
		require_once('models/client/client_address.php');
		require_once('models/international/international_country.php');
		
		
		$this->Customer = new client_customer();
		$this->Address = new client_address();
		$this->Country = new international_country();
		
		$this->Customer->setCacheable(false);
		$this->Address->setCacheable(false);
		
	}
	
	/**
	 * address list
	 */
	
	public function displayAddressList($type) {

		$customer_id = (int) $_SESSION['client']['customer']['id'];
		$addresses = $this->Address->listing("customer_id = {$customer_id}", "id DESC");
		
		foreach ($addresses as $addr) {
					
			$country_detail = $this->Country->detail($addr['country_id']);
			$addr['country'] = $country_detail;
			$this->tpl->assign('ADDRESS', $addr);
			
			if ($addr['id'] == $_SESSION['client']['customer']["{$type}_address_id"]) {
				$this->tpl->assign('ADDRESS_selected', 'selected="selected"');
				$this->tpl->assign('ADDRESS_checked', 'checked="checked"');
			}
			else {
				$this->tpl->assign('ADDRESS_selected', '');
				$this->tpl->assign('ADDRESS_checked', '');
			}
			
			if ($addr['line_2'] != '') $this->tpl->parse('content.address.line_2');
			if ($addr['line_3'] != '') $this->tpl->parse('content.address.line_3');
		
			if ($_SESSION['client']['customer']['delivery_address_id'] == $addr['id']) {
				$this->tpl->assign('ACTIVE', 'active');
				//$this->tpl->parse('content.address.selected');
			} else {
				$this->tpl->assign('ACTIVE', '');
				//$this->tpl->parse('content.address.delete');
				//$this->tpl->parse('content.address.select');
			}
			
			if ($addr['is_deleted']) {
				//display only when it's an active address
				if ($addr['id'] == $_SESSION['client']['customer']['invoices_address_id'] || $addr['id'] == $_SESSION['client']['customer']['delivery_address_id']) {
					$this->tpl->parse('content.address');
				}
			} else {
				$this->tpl->parse('content.address');
			}
			
			
		}
	}

	
	/**
	 * country list
	 */
	 
	public function displayCountryList() {
		
		$countries = $this->Country->listing("", "name ASC");
		
		
		if (!isset($_POST['client']['address']['country_id'])) $_POST['client']['address']['country_id'] = $this->Country->conf['default_id'];
		
		foreach ($countries as $c) {
		
			if ($c['publish'] == 1) {

				if ($c['id'] == $_POST['client']['address']['country_id']) $c['selected'] = "selected='selected'";
				else $c['selected'] = '';
				
				$this->tpl->assign('country', $c);
				$this->tpl->parse('content.country.item');

			}			
		}
		
		$this->tpl->parse('content.country');
	}
	
	/**
	 * add address
	 */
	 
	public function addAddress() {
	
		$_POST['client']['address']['customer_id'] = $_SESSION['client']['customer']['id'];
		
		if ($address_id = $this->Address->insert($_POST['client']['address'])) {
		
			msg('New address added to your list.');
		
			return $address_id;
		} else {
		
			msg('Address is not valid', 'error');
		
			return false;
		}
			
	}

	/**
	 * edit address
	 */
	 
	public function editAddress() {

		$_POST['client']['address']['customer_id'] = $_SESSION['client']['customer']['id'];

		$types = array('invoices', 'delivery');
		$selected_address_id = $_SESSION['client']['customer']["{$this->GET['type']}_address_id"];

		if ($address_id = $this->Address->insert($_POST['client']['address'])) {

			foreach ($types as $type) {
				if ($selected_address_id == $_SESSION['client']['customer']["{$type}_address_id"])
					$this->selectAddress($address_id, $type);
			}

			$this->Address->deleteAddress($selected_address_id);

			msg('Selected address has been successfully updated.');

			onxshopGoto("page/{$_SESSION['active_pages'][0]}");

		} else {
		
			msg('Address is not valid', 'error');
		
			return false;
		}
			
	}
	
	/**
	 * select address
	 */
	
	public function selectAddress($address_id, $type) {
	
		if (!$this->checkAddressType($type)) return false;
		
		$customer_detail = $this->Customer->detail($_SESSION['client']['customer']['id']);
		$customer_detail["{$type}_address_id"] = $address_id;
		
		if ($this->Customer->update($customer_detail)) {
		
			$_SESSION['client']['customer'] = $customer_detail;
		
		} else {
		
			msg("Cannot select this address", 'error');
		
		}
			
	}
	
	/**
	 * check address type
	 */
	 
	private function checkAddressType($type) {
	
		if ($type == 'delivery' || $type == 'invoices') {
			return true;
		} else {
			msg('invalid address type', 'error');
			return false;
		}
	}
	
	/**
	 * remove address
	 */
	 
	public function removeAddress($address_id_to_remove) {
	
		if (!is_numeric($address_id_to_remove)) return false;
		
		$address_detail = $this->Address->detail($address_id_to_remove);
		
		if ($address_detail['customer_id'] == $_SESSION['client']['customer']['id']) {
		
			if ($this->Address->deleteAddress($address_id_to_remove)) msg('Address has been removed');
			else msg('Cannot remove address', 'error');
			
		} else {
		
			msg("This is not your address!", 'error');
			
		}
				
	}
	
}
