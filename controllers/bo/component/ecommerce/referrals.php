<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/ecommerce/promotion_list.php');
require_once "models/ecommerce/ecommerce_promotion.php";
require_once "models/client/client_customer.php";

class Onxshop_Controller_Bo_Component_Ecommerce_Referrals extends Onxshop_Controller {

	/**
	 * Model instance
	 */
	public $Promotion;


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
		$this->Customer = new client_customer();
		$this->Customer->setCacheable(FALSE);


		// render
		$customer_id = $this->GET['customer_id'];
		$this->parseRecentPromotions($customer_id);

		return true;
	}



	/**
	 * Load and display recent promitions
	 * @param int $customer_id Customer id
	 * @return int Total number of referrals
	 */
	protected function parseRecentPromotions($customer_id)
	{
		// prepare list of recent promotions
		$promotions = $this->Promotion->listing("code_pattern LIKE 'REF-%' " . 
			"AND generated_by_customer_id = $customer_id");
		$numReferrals = 0;

		foreach ($promotions as $promotion) {

			$numReferrals += $promotion['uses_per_coupon'];
			$promotion['num_uses'] = $this->Promotion->getCountUsageOfSingleCode($promotion['code_pattern']);
			$this->tpl->assign("PROMOTION", $promotion);

			// parse everything
			$this->tpl->parse("content.promotion_list.item");
		}

		// parse recent promotions list
		$this->tpl->parse("content.promotion_list");
	}

}
