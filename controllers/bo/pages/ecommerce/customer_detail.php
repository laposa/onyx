<?php
/**
 * Customer detail controller
 *
 * Copyright (c) 2005-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('models/common/common_node.php');
require_once('models/client/client_customer.php');
require_once('models/client/client_group.php');
require_once('models/client/client_role.php');
require_once('models/client/client_company.php');

class Onxshop_Controller_Bo_Pages_Ecommerce_Customer_Detail extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {

		$this->Customer = new client_customer();
		$this->Company = new client_company();
		$this->Customer->setCacheable(false);
		$this->Company->setCacheable(false);

		if (is_numeric($this->GET['id'])) $customer_id = $this->GET['id'];
		else $customer_id = 0;
		
		/**
		 * include node configuration
		 */
		
		$node_conf = common_node::initConfiguration();
		$this->tpl->assign('NODE_CONF', $node_conf);

		$this->saveForm($customer_id);		
		$this->parseDetails($customer_id);		

		return true;
	}
	
	/**
	 * saveForm
	 */

	protected function saveForm($customer_id)
	{
		if ($_POST['save']) {

			$_POST['client']['customer']['id'] = $customer_id;

			$this->updatePassword($customer_id);

			if ($this->Customer->updateClient($_POST['client'])) {

				msg('Customer data has been successfully updated.');

			} else {

				msg("Unable to update customer data.", 'error');
			}
			
		}
	}

	/**
	 * updatePassword
	 */
	 
	protected function updatePassword($customer_id)
	{
		if (strlen($_POST['password_new']) > 0) {

			$this->Customer->update(array(
				'id' => $customer_id,
				'password' => md5($_POST['password_new'])
			));

			msg('New password has been set.');
		}

	}
	
	/**
	 * parseDetails
	 */

	protected function parseDetails($customer_id)
	{
		$client = $this->Customer->getClientData($customer_id);
		
		$company_list = $this->Company->listing("customer_id = $customer_id", "id DESC");
		$client['company'] = $company_list[0];

		$client['customer']['newsletter'] = ($client['customer']['newsletter'] == 1) ? 'checked="checked" ' : '';
		$this->tpl->assign('CLIENT', $client);

		if (is_numeric($client['customer']['delivery_address_id']) && $client['customer']['delivery_address_id'] > 0) {
			
			$_Onxshop_Request = new Onxshop_Request("component/client/address~delivery_address_id={$client['customer']['delivery_address_id']}:invoices_address_id={$client['customer']['invoices_address_id']}~");
			$address = $_Onxshop_Request->getContent();
			$this->tpl->assign('ADDRESS', $address);
		
		}

		$this->parseGroupCheckboxes($client['customer']['group_ids']);
		$this->parseRoleCheckboxes($client['customer']['role_ids']);
		$this->parseOtherData($client['customer']['other_data']);

		// Account type dropdown
		$this->tpl->assign("SELECTED_account_type_{$client['customer']['account_type']}", 'selected="selected"');
		$this->tpl->parse('content.account_type');
		
		// Status dropdown
		$this->tpl->assign("SELECTED_status_{$client['customer']['status']}", 'selected="selected"');
		$this->tpl->parse('content.status');

	}

	/**
	 * parseGroupCheckboxes
	 */
	 
	protected function parseGroupCheckboxes($group_ids)
	{
		$ClientGroup = new client_group();
		$list = $ClientGroup->listGroups();

		foreach ($list as $item) {

			$this->tpl->assign('ITEM', $item);

			if (in_array($item['id'], $group_ids)) $this->tpl->assign('CHECKED', 'checked="checked"');
			else $this->tpl->assign('CHECKED', '');

			$this->tpl->parse('content.group.item');

		}
	
		$this->tpl->parse('content.group');
	}

	/**
	 * parseRoleCheckboxes
	 */
	 
	protected function parseRoleCheckboxes($role_ids)
	{
		$ClientRole = new client_role();
		$list = $ClientRole->listRoles();

		foreach ($list as $item) {

			$this->tpl->assign('ITEM', $item);

			if (in_array($item['id'], $role_ids)) $this->tpl->assign('CHECKED', 'checked="checked"');
			else $this->tpl->assign('CHECKED', '');

			$this->tpl->parse('content.role.item');

		}

		$this->tpl->parse('content.role');
	}

	/**
	 * parseOtherData
	 */
	 
	protected function parseOtherData(&$other_data)
	{
		if (is_array($other_data)) {

			foreach ($other_data as $key=>$value) {

				$item = array();

				if (is_array($value)) $item['value'] = print_r($value, true);
				else $item['value'] = $value;

				$item['key'] = $key;

				$this->tpl->assign("ITEM", $item);
				$this->tpl->parse("content.other_data.item");
			}

			if (count($other_data) > 0) $this->tpl->parse("content.other_data");

		}
	}
}
