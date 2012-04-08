<?php
/** 
 * Copyright (c) 2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Referral extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * get active customer id and continue only it's available
		 */
		 
		$customer_id = $this->getActiveCustomerId();

		/**
		 * this is a security check, login should be forced from CMS require_login option
		 */
		 
		if (!is_numeric($customer_id)) {
			msg('component/ecommerce/referral: login required', 'error');
			onxshopGoTo("/");
			return false;
		}
		
		$referral = array();
		$referral['voucher_code'] = '';
		$referral['voucher_value'] = '';
		$this->tpl->assign('REFERRAL', $referral);
 		
		return true;
		
	}
	
	/**
	 * getActiveCustomerId
	 */
	 
	public function getActiveCustomerId() {
	
		if ($_SESSION['client']['customer']['id'] > 0) {
			$customer_id = $_SESSION['client']['customer']['id'];
		} else if ($_SESSION['authentication']['authenticity']  > 0) {
			$customer_id = $this->GET['customer_id'];
		} else {
			$customer_id = false;
		}
	
		return $customer_id;
	}
	
}
