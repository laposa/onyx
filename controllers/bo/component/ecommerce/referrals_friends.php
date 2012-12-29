<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/ecommerce/promotion_list.php');
require_once "models/ecommerce/ecommerce_promotion.php";
require_once "models/ecommerce/ecommerce_promotion_code.php";
require_once "models/ecommerce/ecommerce_invoice.php";
require_once "models/client/client_customer.php";

class Onxshop_Controller_Bo_Component_Ecommerce_Referrals_Friends extends Onxshop_Controller {

	/**
	 * Model instance
	 */
	public $Promotion;

	/**
	 * Model instance
	 */
	public $Promotion_Code;


	/**
	 * main action
	 */
	 
	public function mainAction()
	{
		/**
		 * initializace models
		 */
		$this->Promotion = new ecommerce_promotion();
		$this->Promotion->setCacheable(false);
		$this->Promotion_Code = new ecommerce_promotion_code();
		$this->Promotion_Code->setCacheable(false);
		$this->Customer = new client_customer();
		$this->Customer->setCacheable(FALSE);

		// render
		$customer_id = $this->GET['customer_id'];
		$this->parseFriendsList($customer_id);

		return true;
	}


	/**
	 * Load and display all invited friends
	 * @return [type] [description]
	 */
	protected function parseFriendsList($customer_id)
	{
		// prepare list of invited friends
		$promotions = $this->Promotion->listing("code_pattern LIKE 'REW-%' " .
			"AND limit_by_customer_id  = $customer_id");

		foreach ($promotions as $promotion) {
			$promotion['friend'] = $this->Customer->getDetail($promotion['generated_by_customer_id']);
			$usage = $this->Promotion->getCountUsageOfSingleCode($promotion['code_pattern']);
			$promotion['used'] = $usage > 0 ? "Yes" : "No";
			$promotion['address'] = $this->getAddresses($promotion['generated_by_order_id']);
			$this->tpl->assign("ITEM", $promotion);
			$this->tpl->parse("content.friends_list.item");
		}

		// parse invited friends
		if (count($promotions) == 0) $this->tpl->parse("content.friends_list.none");
		$this->tpl->parse("content.friends_list");
	}

	protected function getAddresses($order_id)
	{
		$Invoice = new ecommerce_invoice();
		$Invoice->setCacheable(false);
		$invoice = $Invoice->getInvoiceForOrder($order_id);
		return array(
			"address_invoice" => $invoice['address_invoice'],
			"address_delivery" => $invoice['address_delivery']
		);
	}

}

