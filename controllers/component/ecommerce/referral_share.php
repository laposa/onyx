<?php
/** 
 * Copyright (c) 2012-2013 Laposa Ltd (http://laposa.co.uk)
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

		$default_message = "Hello," .
				"\n\n" .
				"JING sources exceptional teas from across the world, and designs modern and " . 
				"elegant JINGware specifically designed to infuse their teas." .
				"\n\n" .
				"As we all appreciate tea, I have chosen you to receive £5.00 off when you " .
				" spend over " . money_format("%n", self::MINIMUM_ORDER_AMOUNT) . " on your first order." .
				"\n\n" .
				"Your £5.00 discount voucher is: " . $code . "." .
				"\n\n" .
				"If you use this code I shall also receive a £5.00 discount on my next order over " . 
				money_format("%n", self::MINIMUM_ORDER_AMOUNT) .
				"\n\n" .
				"  Browse JING Tea’s range at http://jingtea.com, and don’t forget to use your code at the checkout." .
				"\n\n" .
				"  With warm regards, \n" .
				$_SESSION['client']['customer']['first_name'];

		$jing_message = "\n\n--\n\n" .
			"This email was sent by " .
			$_SESSION['client']['customer']['first_name'] . " " .  $_SESSION['client']['customer']['last_name'] .
			" who already enjoys JING and thought you would be interested in JING’s range of teas and JINGware. " .
			"If you do not know this person, please let us know by replying to <a href=\"mailto:customerservices@jingtea.com\">customerservices@jingtea.com</a>.";

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
			// create jing url clickable
			$content = str_replace("http://jingtea.com", '<a href="http://jingtea.com">http://jingtea.com</a>', $content);

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
			translateURL("page/" . self::REFERRAL_PAGE_ID) . "?code=" . $code;
	}


}
