<?php
/** 
 * Copyright (c) 2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once "models/ecommerce/ecommerce_promotion.php";
require_once "models/ecommerce/ecommerce_promotion_code.php";
require_once "models/client/client_customer.php";
require_once 'models/common/common_email.php';

class Onxshop_Controller_Component_Ecommerce_Referral extends Onxshop_Controller {

	/**
	 * Total number of referrals every customer can have.
	 * 
	 * This could be constant for now, but there is a plan
	 * to make this variable adjustable (per user group)
	 */
	const AVAILABLE_REFERRALS_PER_PERSON = 10;

	/**
	 * An amount both customers (inviting and invited) will receive
	 * 
	 * This could be constant for now, but there is a plan
	 * to make this variable adjustable (per user group)
	 */
	const DISCOUNT_VALUE = 5;

	/**
	 * Mimimum order amount to placed
	 * 
	 * This could be constant for now, but there is a plan
	 * to make this variable adjustable (per user group)
	 */
	const MINIMUM_ORDER_AMOUNT = 20;

	/**
	 * "Welcome to Jing Tea" page id
	 * 
	 * This could be constant for now, but there is a plan
	 * to make this variable adjustable (per user group)
	 */
	const REFERRAL_PAGE_ID = 5727;



	/**
	 * Model instance
	 */
	public $Promotion;

	/**
	 * Model instance
	 */
	public $Promotion_Code;

	/**
	 * Model instance
	 */
	public $Customer;


	/**
	 * initialisation
	 */
	public function init()
	{
		$this->initModels();
		$this->initCustomer();
		$this->loadPromotion();

		$this->tpl->assign("DISCOUNT_VALUE", self::DISCOUNT_VALUE);
	}

	/**
	 * main action
	 */
	 
	public function mainAction() {

		$this->init();
		if (!$this->securityCheck($this->customer_id)) return false;

		if ($this->promotion) {

			$this->parsePromotion();

			if ($this->promotion['available_uses'] == 0) {
				$this->tpl->parse("content.my_referrals.no_referrals_available");
			} 

		} else {

			// no promotion - display generate button
			$this->tpl->parse("content.referral_generator");
		}

		return true;

	}



	/**
	 * initialise models
	 */
	protected function initModels()
	{

		$this->Promotion = new ecommerce_promotion();
		$this->Promotion->setCacheable(false);
		$this->Promotion_Code = new ecommerce_promotion_code();
		$this->Promotion_Code->setCacheable(false);
		$this->Customer = new client_customer();
		$this->Customer->setCacheable(false);

	}



	/**
	 * set active customer id
	 */
	protected function initCustomer()
	{
		$this->customer_id = $this->getActiveCustomerId();
		$this->customer_group_id = $_SESSION['client']['customer']['group_id'];
	}



	/**
	 * security check, login should be forced from CMS require_login option
	 */
	protected function securityCheck($customer_id)
	{
		if (!is_numeric($customer_id)) {
			msg('component/ecommerce/referral: login required', 'error');
			onxshopGoTo("/");
			return false;
		}

		return true;
	}



	protected function loadPromotion()
	{
		$promotions = $this->Promotion->listing("code_pattern LIKE 'REF-%' " . 
			"AND generated_by_customer_id = {$this->customer_id}");

		$this->promotion_id = (int) $promotions[0]['id'];
		if ($this->promotion_id == 0) return;

		$p = $this->Promotion->getDetail($this->promotion_id);
		if ($p['generated_by_customer_id'] != $this->customer_id) return;

		if ($p) {
			$p['num_uses'] = $this->Promotion->getCountUsageOfSingleCode($p['code_pattern']);
			$p['available_uses'] = max(0, $p['uses_per_coupon'] - $p['num_uses']);
			$this->promotion = $p;
		}

	}



	/**
	 * Get active customer Id
	 */
	protected function getActiveCustomerId() {
	
		if ($_SESSION['client']['customer']['id'] > 0) {
			$customer_id = $_SESSION['client']['customer']['id'];
		} else if ($_SESSION['authentication']['authenticity']  > 0) {
			$customer_id = $this->GET['customer_id'];
		} else {
			$customer_id = false;
		}
	
		return $customer_id;
	}



	/**
	 * Display promotion code
	 */
	protected function parsePromotion()
	{
		$this->promotion['used_class'] = $this->promotion['available_uses'] > 0 ? "unused" : "used";
		$this->tpl->assign("PROMOTION", $this->promotion);
		$this->tpl->parse("content.my_referrals.promotion");
		$this->tpl->parse("content.my_referrals");
	}


}
