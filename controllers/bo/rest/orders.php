<?php
/** 
 * PROTOTYPE: Orders export
 *
 * Copyright (c) 2008-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * http://jing.localdev.onxshop.com/request/bo/export/xml_orders?method=getOrders&who=Davey&when=Day
 */
 
class serviceClass {

	public function sayHello($who, $when)
	{
	    return "Hello $who, Good $when";
	}

	public function getOrders() {
		require_once('models/ecommerce/ecommerce_order.php');
		$Order = new ecommerce_order();
		
		$filter = array();
		$filter['status'] = 1;
		$customer_id = 0;
		$order_list = $Order->getFullDetailList($customer_id, $filter);
		$result = json_encode($order_list);
		return $result;
	}
	
	public function returnXML() {
		$xml = "<?xml version='1.0'?> 
<document>
 <title>Forty What?</title>
 <from>Joe</from>
 <to>Jane</to>
 <body>
  I know that's the answer -- but what's the question?
 </body>
</document>";
		$xml = simplexml_load_string($xml);
    	return $xml;
	}
}


class Onxshop_Controller_Bo_REST_Orders extends Onxshop_Controller {

	public function mainAction() {
		/**
		 * Say Hello
		 *
		 * @param string $who
		 * @param string $when
		 * @return string
		 */
		
		require_once('Zend/Rest/Server.php');
		
		$server = new Zend_Rest_Server();
		
		try {
		    $server->setClass('serviceClass');
		} catch (Zend_Exception $e) {
		    echo "Caught exception: " . get_class($e) . "\n";
		    echo "Message: " . $e->getMessage() . "\n";
		}
		
		$server->handle();
	}
	
	public function mainActionX() {
		require_once('models/ecommerce/ecommerce_order.php');
		
		$Order = new ecommerce_order();
		
		$filter = array();
		$filter['status'] = 1;
		$customer_id = 0;
		
		$order_list = $Order->getFullDetailList($customer_id, $filter);
		
		//print_r($order_list); exit;

		
		if (is_array($sitemap)) {
			$this->tpl->assign("ITEM", $item);
			$this->tpl->parse("content.item");
		}
		
		header('Content-Type: text/xml; charset=UTF-8');

		return true;
	}

}
