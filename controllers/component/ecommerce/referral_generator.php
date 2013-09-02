<?php
/** 
 * Copyright (c) 2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once "controllers/component/ecommerce/referral.php";

class Onxshop_Controller_Component_Ecommerce_Referral_Generator extends Onxshop_Controller_Component_Ecommerce_Referral {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		$this->init();
		if (!$this->securityCheck($this->customer_id)) return false;

		$this->tpl->parse("content.promotion_generator");

		// generate vouchers if user clicked the button
		if ($this->GET['generate'] === 'true') {

			$customer_name = $this->getActiveCustomerName();
			$title = $customer_name . "'s invitation";
			$this->createCodeByCustomer($this->customer_id, $title, self::DISCOUNT_VALUE);

			// redirect to itself
			onxshopGoTo($this->GET["translate"]);
		}

		return true;

	}



	/**
	 * Get active customer name (including title)
	 * @return String Customer's title, first name and last name
	 */
	protected function getActiveCustomerName() {

		return $_SESSION['client']['customer']['title_before'] .
				" " . $_SESSION['client']['customer']['first_name'] . " " .
				$_SESSION['client']['customer']['last_name'];
	}


	/**
	 * Generate and save new customers voucher code
	 * @param  int $customer_id     Customer id
	 * @param  int $uses_per_coupon [description]
	 * @return boolean
	 */
	protected function createCodeByCustomer($customer_id, $title, $discount_value)
	{
		$data = array(
			'title' => $title,
			'description' => '',
			'publish' => 1,
			'code_pattern' => $this->Promotion->generateRandomCode('REF-', 5, 5),
			'discount_fixed_value' => $discount_value,
			'discount_percentage_value' => 0,
			'discount_free_delivery' => 0,
			'uses_per_coupon' => self::AVAILABLE_REFERRALS_PER_PERSON,
			'uses_per_customer' => 1,
			'limit_list_products' => '',
			'other_data' => NULL,
			'limit_delivery_country_id' => 0,
			'limit_delivery_carrier_id' => 0,
			'limit_by_customer_id' => NULL,
			'limit_to_first_order' => 1,
			'limit_to_order_amount' => self::MINIMUM_ORDER_AMOUNT,
			'generated_by_order_id' => NULL,
			'generated_by_customer_id' => $customer_id
		);

		return $this->Promotion->insert($data);
	}




}
