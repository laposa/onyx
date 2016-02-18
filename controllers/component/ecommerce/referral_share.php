<?php
/** 
 * Copyright (c) 2012-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once "controllers/component/ecommerce/referral.php";

class Onxshop_Controller_Component_Ecommerce_Referral_Share extends Onxshop_Controller_Component_Ecommerce_Referral {

	/**
	 * main action
	 */
	 
	public function mainAction()
	{
		$this->init();
		if (!$this->securityCheck($this->customer_id)) return false;

		if ($this->promotion && $this->promotion['available_uses'] > 0) {

			$this->promotion['code_url'] = $this->getShareUrl($this->promotion['code_pattern']);
			$this->processShareRequest($this->promotion['code_pattern']);
			$this->tpl->assign("PROMOTION", $this->promotion);
			$this->tpl->parse("content.share");

		}

		return true;

	}

	/**
	 * Process share request (form submission)
	 */
	protected function processShareRequest($code)
	{
		$referral = array();

		$min = money_format("%n", $this->conf['minimum_order_amount']);
		$discount = money_format("%n", $this->conf['discount_value']);

		$default_message = "Hello," .
				"I have chosen you to receive $discount off when you " .
				" spend over $min on your first order." .
				"\n\n" .
				"Your $discount discount voucher is: $code" .
				"\n\n" .
				"If you use this code I shall also receive a $discount discount on my next order over $min" . 
				"\n\n" .
				"

With warm regards,
\n" .
				$_SESSION['client']['customer']['first_name'];

		$custom_message = "\n\n--\n\n" .
			"This email was sent by " .
			$_SESSION['client']['customer']['first_name'] . " " .  $_SESSION['client']['customer']['last_name'];

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

			$content .= $custom_message;

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
						$fromEsc = pg_escape_string($from);

						$count = $EmailForm->count("email_recipient = '$emailEsc' AND email_from = '$fromEsc' AND template = 'referral_invite'");
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
			translateURL("page/" . $this->conf['referral_page_id']) . "?code=" . $code;
	}


}
