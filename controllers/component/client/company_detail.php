<?php
/**
 * Copyright (c) 2009-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Client_Company_Detail extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		if (is_numeric($this->GET['company_id'])) $company_id = $this->GET['company_id'];
		else if (is_numeric($this->GET['customer_id'])) $customer_id = $this->GET['customer_id'];
		else return false;
		
		require_once('models/client/client_company.php');
		
		$Company = new client_company();
		
		/**
		 * find a company for customer
		 */
		if ($customer_id) {
			$company_list = $Company->getCompanyListForCustomer($customer_id);
			if (count($company_list) > 0) $company_id = $company_list[0]['id']; 
		}
		
		if (is_numeric($company_id)) {
			$company_data = $Company->getDetail($company_id);
			$this->tpl->assign('ITEM', $company_data);
			$this->tpl->parse('content.detail');
		} else {
			$this->tpl->parse('content.empty');
		}
		
		return true;
	}
}
