<?php
/** 
 * Copyright (c) 2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once "controllers/component/ecommerce/referral.php";

class Onxshop_Controller_Component_Ecommerce_Referral_List_Joins extends Onxshop_Controller_Component_Ecommerce_Referral {

	/**
	 * main action
	 */
	 
	public function mainAction()
	{
		$this->init();
		if (!$this->securityCheck($this->customer_id)) return false;

		$promotions = $this->loadList($this->customer_id);

		if (!empty($promotions)) {
			$this->displayList($promotions, $this->customer_id);
		}

		return true;

	}



	protected function loadList($customer_id)
	{
		return $this->Promotion->listing("code_pattern LIKE 'REW-%' " .
			"AND limit_by_customer_id  = $customer_id", "created DESC");
	}



	protected function displayList(&$promotions, $customer_id)
	{
		foreach ($promotions as $promotion) {

			$promotion['friend'] = $this->Customer->getDetail($promotion['generated_by_customer_id']);
			$this->tpl->assign("ITEM", $promotion);
			$this->tpl->parse("content.friends_list.item");
		}

		$this->tpl->parse("content.friends_list");
	}

}
