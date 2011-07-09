<?php
/**
 * Customer detail controller
 *
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Pages_Ecommerce_Customer_Detail extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		if (is_numeric($this->GET['id'])) $customer_id = $this->GET['id'];
		else $customer_id = 0;
		
		if(isset($_POST['save'])) {
			onxshopGoTo('/backoffice/customers');
		}
		
		/**
		 * include node configuration
		 */
		
		require_once('models/common/common_node.php');
		$node_conf = common_node::initConfiguration();
		$this->tpl->assign('NODE_CONF', $node_conf);
		
		/**
		 * get customer detail
		 */
		
		require_once('models/client/client_customer.php');
		$Customer = new client_customer();
		
		$customer_data = $Customer->getDetail($customer_id);
		$this->tpl->assign("CUSTOMER", $customer_data);
		
		$_nSite = new nSite("component/client/address~delivery_address_id={$customer_data['delivery_address_id']}:invoices_address_id={$customer_data['invoices_address_id']}~");
		$address = $_nSite->getContent();
		$this->tpl->assign('ADDRESS', $address);

		//print other data
		if (is_array($customer_data['other_data'])) {
			foreach ($customer_data['other_data'] as $key=>$value) {
				$item = array();
				$item['value'] = $value;
				$item['key'] = $key;
				$this->tpl->assign("ITEM", $item);
				$this->tpl->parse("content.other_data.item");
			}
			if (count($customer_data['other_data']) > 0) $this->tpl->parse("content.other_data");
		}
		
		return true;
	}
}
