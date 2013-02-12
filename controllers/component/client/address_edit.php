<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Client_Address_Edit extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * initialize
		 */
	
		require_once('models/client/client_customer.php');
		require_once('models/client/client_address.php');
		require_once('models/international/international_country.php');
		
		$Customer = new client_customer();
		$Address = new client_address();
		$Country = new international_country();
		
		$Customer->setCacheable(false);
		$Address->setCacheable(false);
		
		
		/**
		 * add address
		 */
		 
		if ($_POST['add_address']) {
		
			$_POST['client']['address']['customer_id'] = $_SESSION['client']['customer']['id'];
			
			if ($address_id = $Address->insert($_POST['client']['address'])) {
			
				msg('New address added to your list. Please select with "use this" button.');
			
			} else {
			
				msg('Address is not valid', 'error');
			
			}
		}
		
		/**
		 * select address
		 */
		
		if ($_POST['select_address']) {
		
			$customer_detail = $Customer->detail($_SESSION['client']['customer']['id']);
			$customer_detail["{$this->GET['type']}_address_id"] = $_POST['select_address'];
			
			if ($Customer->update($customer_detail)) {
			
				$_SESSION['client']['customer'] = $customer_detail;
				onxshopGoTo($_SESSION['referer'], 2);
			
			} else {
			
				msg("Cannot select this address", 'error');
			
			}
		}
		
		/**
		 * remove address
		 */
		 
		if (is_numeric($_POST['remove_address'])) {
		
			$address_id_to_remove = $_POST['remove_address'];
			
			$address_detail = $Address->detail($address_id_to_remove);
			
			if ($address_detail['customer_id'] == $_SESSION['client']['customer']['id']) {
			
				if ($Address->deleteAddress($address_id_to_remove)) msg('Address has been removed');
				else msg('Cannot remove address', 'error');
				
			} else {
			
				msg("This is not your address!", 'error');
				
			}
			
		}
		
		/**
		 * address list
		 */
		
		$addresses = $Address->listing("customer_id = {$_SESSION['client']['customer']['id']} AND is_deleted IS NOT TRUE", "id DESC");
		
		foreach ($addresses as $addr) {
		
			$country_detail = $Country->detail($addr['country_id']);
			$addr['country'] = $country_detail;
			$this->tpl->assign('address', $addr);
			
			if ($addr['line_2'] != '') $this->tpl->parse('content.address.line_2');
			if ($addr['line_3'] != '') $this->tpl->parse('content.address.line_3');
		
			if ($this->GET['type'] != '') $this->tpl->parse('content.address.select');
			else $this->tpl->parse('content.address.delete');
			
			$this->tpl->parse('content.address');
			
		}
		
		/**
		 * country list
		 */
		 
		$countries = $Country->listing("", "name ASC");
		
		if (!isset($_POST['client']['address']['country_id'])) $_POST['client']['address']['country_id'] = $Country->conf['default_id'];
		
		foreach ($countries as $c) {
		
			if ($c['id'] == $_POST['client']['address']['country_id']) $c['selected'] = "selected='selected'";
			else $c['selected'] = '';
			
			$this->tpl->assign('country', $c);
			$this->tpl->parse('content.country.item');
			
		}
		
		$this->tpl->parse('content.country');
		
		/**
		 * assign to template
		 */
		
		$this->tpl->assign('client', $_POST['client']);

		return true;
	}
}
