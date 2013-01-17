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
	public $Customer;

	/**
	 * main action
	 */
	 
	public function mainAction() {

		/**
		 * initializace models
		 */
		$this->Promotion = new ecommerce_promotion();
		$this->Promotion->setCacheable(false);
		$this->Promotion_Code = new ecommerce_promotion_code();
		$this->Promotion_Code->setCacheable(false);
		$this->Customer = new client_customer();
		$this->Customer->setCacheable(FALSE);

		/**
		 * get active customer id and continue only if it's available
		 */

		$customer_id = $this->getActiveCustomerId();
		$customer_group_id = $_SESSION['client']['customer']['group_id'];

		/**
		 * this is a security check, login should be forced from CMS require_login option
		 */

		if (!is_numeric($customer_id)) {
			msg('component/ecommerce/referral: login required', 'error');
			onxshopGoTo("/");
			return false;
		}

		$page_url = translateURL("page/" . self::REFERRAL_PAGE_ID);
		$this->tpl->assign("FAQ_URL", $page_url);

		// display recent promotions (voucher code campaigns)
		$promotions = $this->getCustomerPromotions($customer_id);
		$availableReferrals = $this->getAvailableReferrals($promotions);
		$availableUses = $this->getAvailableUses($promotions);
		$this->parseRecentPromotions($promotions);

		if (count($promotions) > 0) {

			// display invited friends list
			$this->parseFriendsList($customer_id);

			if ($availableUses > 0) {

				// display share page
				$promotion = $this->Promotion->getDetail($promotions[0]['id']);
				if ($promotion['generated_by_customer_id'] != $customer_id) {
					msg('component/ecommerce/referral: access denied', 'error');
					onxshopGoTo("/");
					return false;
				}
				$promotion['code_url'] = $this->getShareUrl($promotion['code_pattern']);

				$this->processShareRequest($promotion['code_pattern']);

				$this->tpl->assign("PROMOTION", $promotion);
				$this->tpl->parse("content.my_referrals.share");
				$this->tpl->assign("AVAILABLE_USES", $availableUses);
				$this->tpl->parse("content.my_referrals.available_uses");

			} else {
				$this->tpl->assign("AVAILABLE_REFERRALS_PER_PERSON", self::AVAILABLE_REFERRALS_PER_PERSON);
				$this->tpl->parse("content.my_referrals.no_referrals_available");
			}

			$this->tpl->assign("DISCOUNT_VALUE", self::DISCOUNT_VALUE);
			$this->tpl->parse("content.my_referrals");

		} else {

			// display voucher generating facility (if user is allowed to do so)
			if (/*$customer_group_id > 0 &&*/ $availableReferrals > 0) { 

				$this->tpl->assign("AVAILABLE_REFERRALS", $availableReferrals);
				$this->tpl->parse("content.promotion_generator");

				// generate vouchers if user clicked the button
				if ($this->GET['generate'] === 'true') {

					// get customer name
					$customer_name = $this->getActiveCustomerName();
					// create voucher code title
					$title = $customer_name . "'s invitation";
					// generate and save voucher
					$this->createCodeByCustomer($customer_id, $title, self::DISCOUNT_VALUE,
						$availableReferrals);
					// redirect to itself
					onxshopGoTo($this->GET["translate"]);
				}

			}

		}

		return true;

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
	 * Get active customer name (including title)
	 * @return String Customer's title, first name and last name
	 */
	protected function getActiveCustomerName() {

		return $_SESSION['client']['customer']['title_before'] .
				" " . $_SESSION['client']['customer']['first_name'] . " " .
				$_SESSION['client']['customer']['last_name'];
	}


	/**
	 * Get number of available referals
	 * 
	 * @param  Array $promotions Array of voucher codes
	 * @return int
	 */
	protected function getAvailableReferrals($promotions)
	{
		$availableReferrals = 0;

		foreach ($promotions as $promotion)
			$availableReferrals += $promotion['uses_per_coupon'];

		return self::AVAILABLE_REFERRALS_PER_PERSON - $availableReferrals;
	}


	/**
	 * Get number of available uses of all generated codes
	 * 
	 * @param  Array $promotions Array of voucher codes
	 * @return int
	 */
	protected function getAvailableUses($promotions)
	{
		$availableUses = 0;

		foreach ($promotions as $promotion)
			$availableUses += $promotion['available_uses'];

		return $availableUses;
	}

	/**
	 * Load and display recent promotions
	 */
	protected function parseRecentPromotions($promotions)
	{
		foreach ($promotions as $promotion) {

			$promotion['used_class'] = $promotion['available_uses'] > 0 ? "unused" : "used";
			$this->tpl->assign("PROMOTION", $promotion);

			// parse everything
			$this->tpl->parse("content.my_referrals.share.promotion_list.item");

		}

		// parse recent promotions list (if any)
		if (count($promotions) > 0) 
			$this->tpl->parse("content.my_referrals.share.promotion_list");

	}



	/**
	 * Get all codes generated by customer
	 * @param  [type] $customer_id [description]
	 * @return [type]              [description]
	 */
	protected function getCustomerPromotions($customer_id)
	{
		$promotions = $this->Promotion->listing("code_pattern LIKE 'REF-%' " . 
			"AND generated_by_customer_id = $customer_id");

		foreach ($promotions as &$promotion) {

			$promotion['num_uses'] = $this->Promotion->getCountUsageOfSingleCode($promotion['code_pattern']);
			$promotion['available_uses'] = max(0, $promotion['uses_per_coupon'] - $promotion['num_uses']);
		}

		return $promotions;
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
			$usage = $this->Promotion_Code->getUsageOfSingleCode($promotion['code_pattern'], $customer_id);
			$age = time() - strtotime($usage[0]['created']);
			if ($usage && $age > 3600 * 24 * 30) continue; // skip 30-days old used codes
			$promotion['used'] = $usage ? "Yes" : "No";
			$promotion['used_class'] = $usage > 0 ? "used" : "green";
			$this->tpl->assign("ITEM", $promotion);
			$this->tpl->parse("content.my_referrals.vouchers_list.item");
			$this->tpl->parse("content.my_referrals.friends_list.item");
		}

		// parse invited friends
		if (count($promotions) == 0) {
			$this->tpl->parse("content.my_referrals.friends_list_empty");
		} else {
			$this->tpl->parse("content.my_referrals.vouchers_list");
			$this->tpl->parse("content.my_referrals.friends_list");
		}

		// parse send emails
		$EmailForm = new common_email();
		$email = pg_escape_string($_SESSION['client']['customer']['email']);
		$emails = $EmailForm->listing("email_from = '$email' AND template = 'referral_invite'");
		if (is_array($emails) && count($emails) > 0) {
			foreach ($emails as $email) {
				$this->tpl->assign("ITEM", $email);
				$this->tpl->parse("content.my_referrals.email_list.email");
			}
			$this->tpl->parse("content.my_referrals.email_list");
		}

	}



	/**
	 * Generate and save new customers voucher code
	 * @param  int $customer_id     Customer id
	 * @param  int $uses_per_coupon [description]
	 * @return boolean
	 */
	protected function createCodeByCustomer($customer_id, $title, $discount_value, $uses_per_coupon)
	{
		$data = array(
			'title' => $title,
			'description' => '',
			'publish' => 1,
			'code_pattern' => $this->Promotion->generateRandomCode('REF-', 5, 5),
			'discount_fixed_value' => $discount_value,
			'discount_percentage_value' => 0,
			'discount_free_delivery' => 0,
			'uses_per_coupon' => $uses_per_coupon,
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



	/**
	 * Process share request (form submission)
	 */
	protected function processShareRequest($code)
	{
		$referral = array();

		$default_message = "Hello,\n\n" .
				"I would like to introduce you to JING Tea. JING sources exceptional teas " .
				"from across the world and designs modern and elegant JINGwares specifically " .
				"designed to infuse their teas.\n\n" .
				"To introduce you to their range I would like to offer you £5.00 off when " . 
				"you spend over £20.00 on your first order. Your JING voucher code is: " . $code .
				"\n\nBrowse JING Tea’s range at http://www.jingtea.com" .
 				"\n\nKind regards,\n" .
				$_SESSION['client']['customer']['first_name'];

		$jing_message = "\n\n--\n\nYou have been referred to us by " . $_SESSION['client']['customer']['first_name'] . " " . 
			$_SESSION['client']['customer']['last_name'] . " as they've enjoyed their experience with JING. " .
			"They thought you would be interested in our range of teas and teawares too. If you do not know " .
			"this person, please let us know by replying to <a href=\"mailto:customerservices@jingtea.com\">" . 
			"customerservices@jingtea.com</a>.";

		// process form
		if ($_POST['referral']['send'] == 'send') {

			$referral['message'] = $_POST['referral']['message'];
			$referral['recipient'] = $_POST['referral']['recipient'];

			$EmailForm = new common_email();
			$EmailForm->setCacheable(false);

			// set sender
			$from = $_SESSION['client']['customer']['email'];
			$from_name = $_SESSION['client']['customer']['first_name'] . " " . $_SESSION['client']['customer']['last_name'];

			// set customer's message (remove CRs so it can be compared)
			$content = preg_replace("/\r\n/", "\n", $referral['message']);

			// amend message from Jing, if a customer changed the message
			// if (strcmp($content, $default_message) != 0) $content .= $jing_message2; 
			$content .= $jing_message;

			// pass variable to email template
			$GLOBALS['common_email']['customer'] = $_SESSION['client']['customer'];

			$numSent = 0;
			$emails = explode(",", $referral['recipient']);
			$msg = '';
			$unsentEmails = array();

			if (is_array($emails)) {

				foreach ($emails as $email) {

					$email = trim($email);
					$customer = $this->Customer->getClientByEmail($email);

					if ($customer) {
						$msg .= "Customer $email is already registered.";
						$unsentEmails[] = $email;
					} else {
						$emailEsc = pg_escape_string($email);

						$count = $EmailForm->count("email_recipient = '$emailEsc' AND template = 'referral_invite'");
						if ($count > 0) {
							$msg .= " You have already invited $email.";
							$unsentEmails[] = $email;
						} else {
							if ($EmailForm->sendEmail('referral_invite', $content, $email, $email, $from, $from_name)) {
								$numSent++;
							} else {
								$msg .= " Unable to send email to $email.";
								$unsentEmails[] = $email;
							}
						}
					}
				}
			}

			if ($numSent == 1) $msg = "One email has been sent. " . $msg;
			else if ($numSent > 1) $msg = "$numSent emails have been sent. " . $msg;
			msg($msg);
			$referral['recipient'] = implode(",", $unsentEmails);

		} else {
			// default message
			$referral['message'] = $default_message;
		}

		$this->tpl->assign("REFERRAL", $referral);
	}

	protected function getShareUrl($code)
	{
		return "http://" . $_SERVER['HTTP_HOST'] . 
			translateURL("page/" . self::REFERRAL_PAGE_ID) . "?code=" . $code;
	}

}
