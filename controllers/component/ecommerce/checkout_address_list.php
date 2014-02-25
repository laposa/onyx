<?php
/** 
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/checkout_address.php');

class Onxshop_Controller_Component_Ecommerce_Checkout_Address_List extends Onxshop_Controller_Component_Ecommerce_Checkout_Address {

	/**
	 * public action
	 */

	public function mainAction() {

		if ($_POST['action'] == 'update_deliver_to_billing') $_SESSION['deliver_to_billing_address'] = (bool) $_POST['deliver_to_billing'];

		parent::mainAction();

		if ($_POST['node_id'] == $this->GET['node_id'] && is_numeric($_POST['selected_address_id'])) onxshopGoto("page/{$_SESSION['active_pages'][0]}");

		return true;
	}

	/**
	 * address list
	 */
	
	public function displayAddressList($type) {

		$customer_id = (int) $_SESSION['client']['customer']['id'];

		$addresses = $this->Address->getRecentAddressList($customer_id, $type);
		
		foreach ($addresses as $addr) {
					
			$country_detail = $this->Country->detail($addr['country_id']);
			$addr['country'] = $country_detail;
			$this->tpl->assign('ADDRESS', $addr);
			
			if ($addr['id'] == $_SESSION['client']['customer']["{$type}_address_id"]) {
				$this->tpl->assign('ADDRESS_selected', 'selected="selected"');
				$this->tpl->parse('content.selected_address');
				if ($type == 'delivery') $delivery = $addr;
				if ($type == 'invoices') $invoices = $addr;
			} else {
				$this->tpl->assign('ADDRESS_selected', '');
			}
			
			// display address only if is not deleted or deleted and active
			$parse = !$addr['is_deleted'] || ($addr['is_deleted'] && 
				($addr['id'] == $_SESSION['client']['customer']['invoices_address_id'] || 
				$addr['id'] == $_SESSION['client']['customer']['delivery_address_id']));

			if ($parse) $this->tpl->parse('content.address');
		}

		if ($type == 'delivery') $this->displayDeliverToBillingCheckbox();
	}

	public function displayDeliverToBillingCheckbox()
	{
		if ($_SESSION['deliver_to_billing_address']) {

			$this->tpl->assign('CHECKED',  'checked="checked"');
			$_SESSION['deliver_to_billing_address'] = true;
			$_SESSION['client']['customer']['delivery_address_id'] = $_SESSION['client']['customer']['invoices_address_id'];
			$this->tpl->parse('content.deliver_to_billing');

		} else {

			$this->tpl->assign('CHECKED', '');
			if ($_SESSION['client']['customer']['invoices_address_id'] != $_SESSION['client']['customer']['delivery_address_id']) $this->tpl->parse('content.deliver_to_billing');
		
		}

	}

}
