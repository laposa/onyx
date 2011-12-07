<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Payment extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$this->transactionPrepare();
		if ($this->mainPaymentAction()) return true;
		
	}
	
	/**
	 * prepare transaction
	 */
	
	function transactionPrepare() {
	
		/**
		 * create transaction object
		 */
		
		require_once('models/ecommerce/ecommerce_transaction.php');
		$this->Transaction = new ecommerce_transaction();
		$this->Transaction->setCacheable(false);
	
	}
	
	/**
	 * main payment action
	 */
	
	public function mainPaymentAction() {

		setlocale(LC_MONETARY, $GLOBALS['onxshop_conf']['global']['locale']);
		
		/**
		 * check input values
		 */
		
		if (is_numeric($this->GET['order_id'])) {
			$order_id = $this->GET['order_id'];
		} else {
			msg('Payment: Missing order_id', 'error');
			return false;
		}
		
		/**
		 * include node configuration
		 */
		
		require_once('models/common/common_node.php');
		$node_conf = common_node::initConfiguration();
		$this->tpl->assign('NODE_CONF', $node_conf);
		
		/**
		 * forward to login
		 */
		
		if ($_SESSION['client']['customer']['id'] == 0) {
			msg('You must login first.');
			onxshopGoTo("/page/" .$node_conf['id_map-login']);
		}
		
		/**
		 * get order detail
		 */
		
		$order_data = $this->Transaction->getOrderDetail($order_id);
		
		// need to assign ORDER detail into template before processing Google Analytics
		$this->tpl->assign("ORDER", $order_data);
		
		/**
		 * google analytics
		 */
		//TODO: NOTE: Do not include the square brackets when setting the values for the form. In addition, do not use commas to separate the thousands place in your total, tax, and shipping fields - any digits after the comma will be dropped.
		if ($GLOBALS['onxshop_conf']['global']['google_analytics'] != '') {
				
			foreach ($order_data['basket']['items'] as $item) {
				$this->tpl->assign("ITEM", $item);
				$this->tpl->parse('content.google_analytics.item');
			}
			
			$this->tpl->parse('content.google_analytics');
		}
		
		/**
		 * Google Adwords, must be numeric
		 */
		 
		if (is_numeric($GLOBALS['onxshop_conf']['global']['google_adwords'])) {
			$this->tpl->parse('content.google_adwords');
		}
		
		/**
		 * find what payment method we use
		 */
		 
		$payment_type = $this->Transaction->getPaymentTypeForOrder($order_id);
		
		/**
		 * check whether payment is supported
		 */
		
		$controller = "component/ecommerce/payment/$payment_type";
		
		if (getTemplateDir($controller . ".html") == '') {
			msg("Unsupported payment type $payment_type", 'error');
			return false;
		}
		
		/**
		 * process payment method only if status = 0 unpaid or 5 failed payment 
		 */
		
		if ($this->checkOrderStatusValidForPayment($order_data['status'])) {
		
			$total_payment_amount = $order_data['basket']['total_after_discount'] + $order_data['basket']['delivery']['value'];
			
			if(round($total_payment_amount, 2) == 0) {
				
				//nil payment - payment is not needed	
				if ($this->processNilPayment($order_data)) {
					$this->tpl->parse('content.nil_payment');
				} else {
					msg("Cannot process nil payment for order ID $order_id", 'error');
				}
			} else {
			
				//process payment method as subcontent
				$_nSite = new nSite("component/ecommerce/payment/$payment_type~order_id=$order_id~");
				$this->tpl->assign("RESULT", $_nSite->getContent());
			
			}
		} else {
		
			msg("Order ID {$order_data['id']} cannot be paid, because order status is: {$order_data['status_title']}", 'error');
			return false;
		
		}
		
		setlocale(LC_MONETARY, LOCALE);
		
		return true;
	}
	
	/**
	 * check order status
	 * process payment method only if status = 0 unpaid or 5 failed payment 
	 */
	
	function checkOrderStatusValidForPayment($status) {
		
		if ($this->Transaction->checkOrderStatusValidForPayment($status)) {
			return true;
		} else {
			msg("This order is already paid.", 'error');
			return false;
		}
	}
	
	
	/**
	 * prepare data for payment gateway
	 */
	
	function paymentPrepare($order_id) {
	
	}
	
	/**
     * process callback
     */
	
	function paymentProcess($order_id, $pg_data) {
	
	}
	
	/**
	 * processNilPayment
	 */
	 
	public function processNilPayment($order_data) {
		
		if (!is_array($order_data)) return false;
		if ($order_data['basket']['total_after_discount'] > 0) return false;
		
		require_once('models/ecommerce/ecommerce_order.php');
		$EcommerceOrder = new ecommerce_order();
		$EcommerceOrder->setCacheAble(false);
		
		//mark as payed
		$log_data_id = $EcommerceOrder->setStatus($order_data['id'], 1);
	
		return $log_data_id;
		
	}
}
