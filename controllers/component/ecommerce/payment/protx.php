<?php
/** 
 * ProtX aka SagePay
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/payment.php');

class Onxshop_Controller_Component_Ecommerce_Payment_Protx extends Onxshop_Controller_Component_Ecommerce_Payment {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('conf/payment/protx.php');
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
    	
    	if ($_SERVER['HTTPS']) $protocol = 'https';
		else $protocol = 'http';
		$server_url = "$protocol://{$_SERVER['HTTP_HOST']}";

    	require_once('lib/protx.functions.php');
	
		$protx = array(
			'URL' => ECOMMERCE_TRANSACTION_PROTX_URL,
			'VPSProtocol' => ECOMMERCE_TRANSACTION_PROTX_VPSPROTOCOL,
			'Vendor' => ECOMMERCE_TRANSACTION_PROTX_VENDOR,
			'TxType' => ECOMMERCE_TRANSACTION_PROTX_TXTYPE,
			'Crypt' => '',
			'VendorEmail' => ECOMMERCE_TRANSACTION_PROTX_VENDOR_EMAIL
		);
		
		require_once('models/ecommerce/ecommerce_order.php');
		$Order = new ecommerce_order();		
		$protx_amount = $Order->calculatePayableAmount($order_data);
	
		$protx['Crypt']['VendorTxCode'] = $order_data['id'] . '_' . time();
		//basket[total] is without delivery
		//$protx['Crypt']['Amount'] = $order_data['basket']['total'];
		$protx['Crypt']['Amount'] = $protx_amount;
		$protx['Crypt']['Currency'] = GLOBAL_DEFAULT_CURRENCY;
		$protx['Crypt']['Description'] = "Payment for Basket created {$order_data['basket']['created']}";
		$protx['Crypt']['SuccessURL'] = "$server_url/page/" . $node_conf['id_map-payment_protx_success'] . "?order_id={$order_data['id']}";
		$protx['Crypt']['FailureURL'] = "$server_url/page/" . $node_conf['id_map-payment_protx_success'] . "?order_id={$order_data['id']}";
		//optional
		$protx['Crypt']['CustomerName'] = $order_data['client']['customer']['title_before'] . ' ' . $order_data['client']['customer']['first_name'] . ' ' . $order_data['client']['customer']['last_name'];
		$protx['Crypt']['CustomerEMail'] = $order_data['client']['customer']['email'];
		$protx['Crypt']['VendorEMail'] = $protx['VendorEmail'];
		$protx['Crypt']['eMailMessage'] = ECOMMERCE_TRANSACTION_PROTX_MAIL_MESSAGE;
		$protx['Crypt']['DeliveryAddress'] = $order_data['address']['delivery']['line_1'];
		$protx['Crypt']['DeliveryPostCode'] = $order_data['address']['delivery']['post_code'];
		$protx['Crypt']['BillingAddress'] = $order_data['address']['invoices']['line_1'];
		$protx['Crypt']['BillingPostCode'] = $order_data['address']['invoices']['post_code'];
		$protx['Crypt']['Basket'] = '';
	
		$basket = count($order_data['basket']['items']);
	
		//Number of items in basket:Item 1 Description:Quantity of item 1:Unit cost item 1 minus tax:Tax of item 1:Cost of Item 1 inc tax:Total cost of item 1 (Quantity x cost inc tax):Item 2 Description:Quantity of item 2: .... :Cost of Item n inc tax:Total cost of item n
		foreach ($order_data['basket']['items'] as $item) {
			$basket = $basket . ':' . $item['product']['variety']['sku'] . ' - ' . $item['product']['name'] . ':' . $item['quantity'] . ':' . $item['product']['variety']['price'][GLOBAL_DEFAULT_CURRENCY]['price']['common']['value'] . ':' . $item['product']['variety']['price'][GLOBAL_DEFAULT_CURRENCY]['vat'] . ':' . $item['product']['variety']['price'][GLOBAL_DEFAULT_CURRENCY]['price']['common']['value_vat'] . ':' . $item['total_inc_vat'];
		}
	
		//echo $basket; exit;
		$protx['Crypt']['Basket'] = $basket;
	
		//print_r($protx);
	
		foreach ($protx['Crypt'] as $key=>$val) {
			$crypt = $crypt . '&' . $key . '=' . $val; 
		}
		$crypt = ltrim($crypt, '&');
	
		$protx['Crypt'] = base64_encode(simpleXor($crypt, ECOMMERCE_TRANSACTION_PROTX_PASSWORD));
		
		
		return $protx;
		
    }
    
    /**
     * process callback
     */
	
	function paymentProcess($order_id, $crypt) {
	
        //hack for changing white space to + sign
		$crypt = str_replace(' ', '+', $crypt);

		require_once('models/ecommerce/ecommerce_order.php');
		$Order = new ecommerce_order();

		require_once('lib/protx.functions.php');
		//decode crypt
		$pg_data_x = simpleXor(base64Decode($crypt), ECOMMERCE_TRANSACTION_PROTX_PASSWORD);
		//explode protx data
		$pg_data = getToken($pg_data_x);
		
		/**
		 * PROTX:
		 * vpstxid [int]
		 * avscv2 [int]
		 * txauthno[int]
		 * vpsstatus[int]
		 */
		/*
		$pg_data_x = explode('&', $pg_data_x);
		for ($i=1; $i<count($pg_data_x); $i++) {
		    $param = explode('=', $pg_data_x[$i]);
	    	$pg_data[$param[0]] = $param[1];
		}
		*/
		//print_r($pg_data);

		// check if $pg_data['VendorTxCode'] = $_GET['order_id']

		$this->msgProtxStatus($pg_data['Status']);

		$order_data = $Order->getOrder($order_id);
		//print_r($order_data);
		
		/**
		 * optional: save only orders in valid status
		 */
		/*
		if ($order_data['status'] == 1 || $order_data['status'] == 2 || $order_data['status'] == 3 || $order_data['status'] == 4) {
			msg("Ecommerce_transaction: Order in status New (paid), Dispatched, Completed, Cancelled", 'error', 2);
			msg("This order (id=$order_id) was already paid before.", 'error');
		}
		*/

		$transaction_data['order_id'] = $order_data['id'];
		$transaction_data['pg_data'] = serialize($pg_data);
		$transaction_data['currency_code'] = GLOBAL_DEFAULT_CURRENCY;
		if (is_numeric($pg_data['Amount'])) $transaction_data['amount'] = $pg_data['Amount'];
		else $transaction_data['amount'] = 0;
		$transaction_data['created'] = date('c');
		$transaction_data['type'] = 'protx';
		if ($pg_data['Status'] == 'OK') $transaction_data['status'] = 1;
		else $transaction_data['status'] = 0;
		
		
		/**
		 * insert
		 */
		 
		if ($id = $this->Transaction->insert($transaction_data)) {
		
		    // in payment_success must be everytime Status OK
			if ($pg_data['Status'] == 'OK') {
				$Order->setStatus($order_id, 1);
				
				//send email to admin
				require_once('models/common/common_email.php');
    
    			$EmailForm = new common_email();
    		
    			$_Onxshop_Request = new Onxshop_Request("component/ecommerce/order_detail~order_id={$order_data['id']}~");
				$order_data['order_detail'] = $_Onxshop_Request->getContent();
				
    			//this allows use customer data and company data in the mail template
    			//is passed as DATA to template in common_email->_format
    			$GLOBALS['common_email']['transaction'] = $transaction_data;
    			$GLOBALS['common_email']['order'] = $order_data;
	    		
    			if (!$EmailForm->sendEmail('new_order_paid', 'n/a', $order_data['client']['customer']['email'], $order_data['client']['customer']['first_name'] . " " . $order_data['client']['customer']['last_name'])) {
    				msg('ecommerce_transaction: Cant send email.', 'error', 2);
    			}
    			
    			if ($Order->conf['mail_to_address']) {
    				if (!$EmailForm->sendEmail('new_order_paid', 'n/a', $Order->conf['mail_to_address'], $Order->conf['mail_to_name'])) {
    					msg('ecommerce_transaction: Cant send email.', 'error', 2);
    				}
    			}
    		
			} else {
				$Order->setStatus($order_id, 5);
			}
			
			return $id;
		} else {
		
			//to be sure...
			if ($pg_data['Status'] == 'OK') {
				msg("Payment for order $order_id was successfully Authorised, but I cant save the transaction TxAuthNo {$pg_data['TxAuthNo']}!", 'error');
			}
			
			msg("payment/protx: cannot insert serialized pg_data: {$transaction_data['pg_data']}", 'error');
			
			return false;
		}

	}
	
	/**
	 * protx status translation
	 * 
	 */
	 
	function msgProtxStatus($status) {
	
		if ($status == 'OK') {
            msg('Process executed without error and the transaction was successfully Authorised.', 'ok', 2);
        } else if ($status == 'MALFORMED') {
            msg('Input message was malformed - normally will only occur during development and vendor integration. StatusDetail will give more information.', 'error');
        } else if ($status == 'INVALID') {
            msg('Unable to authenticate the vendor, values in the fields are illegal or incorrect, or problem occurred registering the transaction. For example, a MALFORMED Status will be sent if the Amount field is missing, but an INVALID will be sent if it contains text or is too large a number for the specified currency.', 'error');
        } else if ($status == 'NOTAUTHED') {
            msg(' The VSP could not authorise the transaction because the details provided by the Customer were incorrect, not authenticated or could not support the Transaction.', 'error');
        } else if ($status == 'ABORT') {
            msg('The Transaction could not be completed because the user clicked the Cancel button on one of the PROTX pages (or the transaction timed out).', 'error');
        } else if ($status == 'ERROR') {
            msg('A code-related error occurred which prevented the process from executing successfully. This indicates something is wrong at the PROTX server.', 'error');
        } else {
			msg("Unknown status $status", 'error');
		}
		
    }
	
}
