<?php
/**
 * ProtX aka SagePay
 * 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/payment/protx.php');

class Onxshop_Controller_Component_Ecommerce_Payment_Protx_Callback extends Onxshop
_Controller_Component_Ecommerce_Payment_Protx {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		if ($_SESSION['client']['customer']['id'] == 0) {
			msg('payment_callback_protx: You must be logged in.');
			onxshopGoTo("/");
		}
		
		require_once('conf/payment/protx.php');
		$this->transactionPrepare();
		
		if (is_numeric($this->GET['order_id']) && $this->GET['crypt'] != '') {
		
			$this->paymentProcess($this->GET['order_id'], $this->GET['crypt']);
		}

		return true;
	}
	
}
