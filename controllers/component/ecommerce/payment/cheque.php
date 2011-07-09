<?php
/** 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/payment.php');

class Onxshop_Controller_Component_Ecommerce_Payment_Cheque extends Onxshop_Controller_Component_Ecommerce_Payment {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		//$cheque = $this->prepare($order_id);	
		$this->tpl->parse('content.cheque');
	
	}
	
	/**
	 * prepare data for payment gateway
	 */
	
    function prepare($order_id) {
    
    	$order_data = $this->Transaction->getOrderDetail($order_id);
    	
    	// send email to the customer
		require_once('models/common/common_email_form.php');
	    
	    $EmailForm = new common_email_form();
	    
	    $_nSite = new nSite("component/ecommerce/order_detail~order_id={$order_data['id']}~");
		$order_data['order_detail'] = $_nSite->getContent();
					
	    //this allows use customer data and company data in the mail template
	    //is passed as DATA to template in common_email_form->_format
	    $GLOBALS['common_email_form']['transaction'] = $transaction_data;
	    $GLOBALS['common_email_form']['order'] = $order_data;
		    		
	    if (!$EmailForm->sendEmail('pay_by_cheque', 'n/a', $order_data['client']['customer']['email'], $order_data['client']['customer']['first_name'] . " " . $order_data['client']['customer']['last_name'])) {
	    	msg('payment: Cant send email pay_by_cheque.', 'error', 2);
	    }
    }
}