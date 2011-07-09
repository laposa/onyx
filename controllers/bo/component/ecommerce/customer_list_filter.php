<?php
/**
 * Backoffice customer list filter
 *
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Customer_List_Filter extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * Store submited data to the SESSION
		 */
		
		if (isset($_POST['customer-list-filter'])) $_SESSION['customer-list-filter'] = $_POST['customer-list-filter'];
		
		if (!is_array($_SESSION['customer-list-filter'])) {
			$_SESSION['customer-list-filter'] = array();
			$_SESSION['customer-list-filter']['invoice_status'] = 0;
			$_SESSION['customer-list-filter']['account_type'] = -1;
		}
		
		/**
		 * With orders and account type options
		 */
		
		$this->tpl->assign("SELECTED_invoice_status_{$_SESSION['customer-list-filter']['invoice_status']}", "selected='selected'");
		$this->tpl->assign("SELECTED_account_type_{$_SESSION['customer-list-filter']['account_type']}", "selected='selected'");
		
		/**
		 * Country list
		 */
		 
		require_once('models/international/international_country.php');
		$Country = new international_country();
		$countries = $Country->listing();
		
		foreach ($countries as $item) {
			if ($item['id'] == $_SESSION['customer-list-filter']['country_id']) $item['selected'] = "selected='selected'";
			else $item['selected'] = '';
			$this->tpl->assign('ITEM', $item);
			$this->tpl->parse('content.country.item');
		}
		
		$this->tpl->parse('content.country');

		/**
		 * product list
		 */
		
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		
		$product_list = $Product->listing('publish = 1', 'name ASC');
		
		if (is_array($product_list) && count($product_list) > 0) {
			foreach ($product_list as $item) {
				
				if ($item['id'] == $_SESSION['customer-list-filter']['product_bought']) $item['selected'] = "selected='selected'";
				else $item['selected'] = '';
			
				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.product.item');
			}
		
			$this->tpl->parse('content.product');
		}
		
		return true;
	}
}		
