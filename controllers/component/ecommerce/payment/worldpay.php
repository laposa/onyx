<?php
/** 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/payment.php');

class Onxshop_Controller_Component_Ecommerce_Payment_Worldpay extends Onxshop_Controller_Component_Ecommerce_Payment {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('conf/payment/worldpay.php');
		$this->transactionPrepare();
		
		$payment_gateway_data = $this->paymentPrepare($this->GET['order_id']);
		
		if (!$payment_gateway_data) return false;
		
		$this->tpl->assign("PAYMENT_GATEWAY", $payment_gateway_data);
		$this->tpl->parse('content.autosubmit');
		
		return true;
		
	}
	
	/**
	 * prepare data for payment gateway
	 */
	
	function paymentPrepare($order_id) {
	
		require_once('models/common/common_node.php');
		$node_conf = common_node::initConfiguration();
		//$this->tpl->assign('NODE_CONF', $node_conf);
		
    	$order_data = $this->Transaction->getOrderDetail($order_id);
    	
 		/**
		 * process payment method only if status = 0 unpaid or 5 failed payment
		 * 
		 */
		 
    	if (!$this->checkOrderStatusValidForPayment($order_data['status'])) return false;
    	
    	if ($_SERVER['HTTPS']) $protocol = 'https';
		else $protocol = 'http';
		$server_url = "$protocol://{$_SERVER['HTTP_HOST']}";
		
		$worldpay_amount = $order_data['basket']['total_goods_net'] + $order_data['basket']['total_vat']  + $order_data['basket']['delivery']['value_net'] + $order_data['basket']['delivery']['vat'];
		$worldpay_amount = round($worldpay_amount, 2);
    	
		$worldpay = array(
			'URL' => ECOMMERCE_TRANSACTION_WORLDPAY_URL,
			'instId' => ECOMMERCE_TRANSACTION_WORLDPAY_INSID,
			'cartId' => $order_data['id'],
			'amount' => $worldpay_amount,
			'currency' => GLOBAL_DEFAULT_CURRENCY,
			'desc' => ECOMMERCE_TRANSACTION_WORLDPAY_DESCRIPTION,
			'testMode' => ECOMMERCE_TRANSACTION_WORLDPAY_TESTMODE,
			'name' => $order_data['client']['customer']['title_before'] . ' ' . $order_data['client']['customer']['first_name'] . ' ' . $order_data['client']['customer']['last_name'],
			'address' => $order_data['address']['invoices']['line_1'],
			'postcode' => $order_data['address']['invoices']['post_code'],
			'country' => $order_data['address']['invoices']['country']['iso_code2'],
			'tel' => $order_data['address']['invoices']['telephone'],
			'email' => $order_data['client']['customer']['email'],
			'MC_callback' => "$server_url/page/" . $node_conf['id_map-payment_worldpay_callback'] . "?order_id={$order_data['id']}"
		);
		
		return $worldpay;
    }
	
    /**
     * process callback
     */
    
	function paymentProcess($order_id, $pg_data) {
		
		require_once('models/ecommerce/ecommerce_order.php');
		$Order = new ecommerce_order();

		// check if $pg_data['VendorTxCode'] = $_GET['order_id']

		//$this->msgProtxStatus($pg_data['Status']);

		$order_data = $Order->getOrder($order_id);
		//print_r($order_data);
		
		/**
		 * optional: process payment method only if status = 0 unpaid or 5 failed payment 
		 * (better to save transaction every time)
		 */
		 
		//if (!$this->checkOrderStatusValidForPayment($order_data['status'])) return false;
		
		$transaction_data['order_id'] = $order_data['id'];
		$transaction_data['pg_data'] = serialize($pg_data);
		$transaction_data['currency_code'] = GLOBAL_DEFAULT_CURRENCY;
		if (is_numeric($pg_data['authCost'])) $transaction_data['amount'] = $pg_data['authCost'];
		else $transaction_data['amount'] = 0;
		$transaction_data['created'] = date('c');
		$transaction_data['type'] = 'worldpay';
		if ($pg_data['transStatus'] == 'Y') $transaction_data['status'] = 1;
		else $transaction_data['status'] = 0;
		
		/**
		 * check installation id
		 */
		
		if ($pg_data['installation'] != ECOMMERCE_TRANSACTION_WORLDPAY_INSID) {
			msg("payment/worldpay: wrong installation id {$pg_data['installation']}, serialized pg_data: {$transaction_data['pg_data']}", 'error');
			return false;
		}
		
		/**
		 * insert
		 */
		 
		if ($id = $this->Transaction->insert($transaction_data)) {
		
		    // in payment_success must be everytime Status OK
			if ($pg_data['transStatus'] == 'Y') {
			
				$Order->setStatus($order_id, 1);
				
				//send email to admin
				require_once('models/common/common_email.php');
    			$EmailForm = new common_email();
    		
    			$_nSite = new nSite("component/ecommerce/order_detail~order_id={$order_data['id']}~");
				$order_data['order_detail'] = $_nSite->getContent();
				
    			//this allows use customer data and company data in the mail template
    			//is passed as DATA to template in common_email->_format
    			$GLOBALS['common_email']['transaction'] = $transaction_data;
    			$GLOBALS['common_email']['order'] = $order_data;
	    		
    			if (!$EmailForm->sendEmail('new_order_paid', 'n/a', $order_data['client']['customer']['email'], $order_data['client']['customer']['first_name'] . " " . $order_data['client']['customer']['last_name'])) {
    				msg("ecommerce_transaction: Can't send email.", 'error', 2);
    			}
    		
				if ($Order->conf['mail_to_address']) {
    				if (!$EmailForm->sendEmail('new_order_paid', 'n/a', $Order->conf['mail_to_address'], $Order->conf['mail_to_name'])) {
    					msg('ecommerce_transaction: Cant send email.', 'error', 2);
    				}
    			}
    		
    			/**
    			 * cancel immediatelly if it was only a test
    			 */
    			
    			if ($pg_data['testMode'] == 100) {
    				
    				$Order->setStatus($order_id, 4);
    				msg("Order #{$order_id} has been cancelled, because Worldpay testMode was active.");
    				
    			}
    			
			} else {
			
				$Order->setStatus($order_id, 5);
			
			}
			
			return $id;
		} else {
		
			//to be sure...
			if ($pg_data['Status'] == 'OK') {
				msg("Payment for order $order_id was successfully Authorised, but I cant save the transaction id {$pg_data['transId']}!", 'error');
			}
			
			msg("payment/worldpay: cannot insert serialized pg_data: {$transaction_data['pg_data']}", 'error');
			
			return false;
		}

	}
	
}
