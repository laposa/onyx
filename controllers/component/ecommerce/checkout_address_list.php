<?php
/** 
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/checkout_address.php');

class Onxshop_Controller_Component_Ecommerce_Checkout_Address_List extends Onxshop_Controller_Component_Ecommerce_Checkout_Address {

	/**
	 * address list
	 */
	
	public function displayAddressList($type) {

		$customer_id = (int) $_SESSION['client']['customer']['id'];

		$addresses = $this->Address->getRecentAddressList($customer_id, $type);
		
		$i = 0;

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

			// display address only if is not deleted or deleted and active
			$parse = !$addr['is_deleted'] || ($addr['is_deleted'] && 
				($addr['id'] == $_SESSION['client']['customer']['invoices_address_id'] || 
				$addr['id'] == $_SESSION['client']['customer']['delivery_address_id']));

			if ($parse) {

				if ($i == 0) {
					$this->tpl->parse('content.selected_address');
				} else {
					$this->tpl->parse('content.address');
				}

				$i++;
			}

			// display just four items
			if ($i == 4) break;

			
		}
	}

}
