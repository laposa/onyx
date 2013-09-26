<?php
/** 
 * Copyright (c) 2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once "controllers/component/ecommerce/referral.php";

class Onxshop_Controller_Component_Ecommerce_Referral_List_Rewards extends Onxshop_Controller_Component_Ecommerce_Referral {

	/**
	 * main action
	 */
	 
	public function mainAction()
	{
		$this->init();
		if (!$this->securityCheck($this->customer_id)) return false;

		$promotions = $this->loadList($this->customer_id);

		if (empty($promotions)) {
			$this->displayEmptyMessage();
		} else {
			$this->displayList($promotions, $this->customer_id);
		}

		return true;

	}



	protected function loadList($customer_id)
	{
		return $this->Promotion->listing("code_pattern LIKE 'REW-%' " .
			"AND limit_by_customer_id  = $customer_id", "created DESC");
	}



	protected function displayEmptyMessage()
	{
		$this->tpl->parse("content.friends_list_empty");
	}



	protected function displayList(&$promotions, $customer_id)
	{
		foreach ($promotions as $promotion) {

			$usage = $this->Promotion_Code->getUsageOfSingleCode($promotion['code_pattern'], $customer_id);
			$age = time() - strtotime($usage[0]['created']);
			if ($usage && $age > 3600 * 24 * 30) continue; // skip 30-days old used codes
			$promotion['used_class'] = $usage > 0 ? "used" : "green";
			$promotion['redeemed'] = $usage[0]['created'];

			$this->tpl->assign("ITEM", $promotion);

			if ($usage > 0)
				$this->tpl->parse("content.vouchers_list.item.redeemed");
			else
				$this->tpl->parse("content.vouchers_list.item.received");

			$this->tpl->parse("content.vouchers_list.item");
		}

		$this->tpl->parse("content.vouchers_list");
	}

}
