<?php
/** 
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * http://www.mattwillo.co.uk/blog/2010-04-13/integrating-paypal-with-php-and-ipn/
 *
 */

require_once('controllers/component/ecommerce/payment.php');

class Onxshop_Controller_Component_Ecommerce_Payment_Paypal extends Onxshop_Controller_Component_Ecommerce_Payment {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
	}
	
	/**
	 * prepare data for payment gateway
	 */
	
	function prepare($order_id) {
    	$order_data = $this->Transaction->getOrderDetail($order_id);
    }
}