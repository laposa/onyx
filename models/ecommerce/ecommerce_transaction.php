<?php
/**
 * class ecommerce_transaction
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_transaction extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;
	/**
	 * REFERENCES order(id) ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	var $order_id;
	/**
	 * serialized
	 * @access private
	 */
	var $pg_data;
	/**
	 * @access private
	 */
	var $currency_code;
	/**
	 * @access private
	 */
	var $amount;

	var $created;
	
	/**
	 * type: protx, worldpay, paypal, cheque, etc
	 * same as payment type (component/ecommerce/payment/)
	 */
	 
	var $type;
	
	/**
	 * 0 invalid
	 * 1 valid
	 *
	 * from protx: OK, MALFORMED, INVALID, NOTAUTHED, ABORT, ERROR
	 * from worldpay: successful, declined, cancelled (transStatus Y, C)
	 */
	 
	var $status;

	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'order_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'pg_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>true),
		'currency_code'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'type'=>array('label' => '', 'validation'=>'string', 'required'=>false),//for the transition keep as not required
		'status'=>array('label' => '', 'validation'=>'int', 'required'=>false)
		);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_transaction (
    id serial NOT NULL PRIMARY KEY,
    order_id integer REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT,
    pg_data text,
    currency_code character(3),
    amount numeric(12,5),
    created timestamp(0) without time zone,
	type varchar(255),
	status smallint
);
		";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
	
		if (array_key_exists('ecommerce_transaction', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_transaction'];
		else $conf = array();

		//what payment types are allow? possible values: protx,worldpay,cheque,paypal
		//first is used as default
		if (array_key_exists('allowed_types', $conf)) {
			$conf['allowed_types'] = explode(',', $conf['allowed_types']);
		} else {
			//default protx,cheque
			$conf['allowed_types'] = array('paypal', 'protx', 'cheque');
		}
		
		return $conf;
		
	}
	
	/**
	 * get single transaction detail
	 *
	 * @param unknown_type $transaction_id
	 * @return unknown
	 */
		
	function getDetail($transaction_id) {
		
		if (!is_numeric($transaction_id)) return false;
		
		$detail = $this->detail($transaction_id);
		$detail['pg_data'] = unserialize($detail['pg_data']);
		
		return $detail;
	}
	
	/**
	 * Get list of all transaction for an order
	 *
	 * @param unknown_type $order_id
	 * @return unknown
	 */
	
	function getListForOrderId($order_id) {
		
		if (!is_numeric($order_id)) return false;
		
		$transaction_list = $this->listing("order_id = $order_id", 'id DESC');
		
		if (is_array($transaction_list) && count($transaction_list) > 0) {
			
			foreach($transaction_list as $i=>$transaction) {
				$transaction_list[$i]['pg_data'] = unserialize($transaction['pg_data']);
			}
			
			return $transaction_list;
		} else {
			return false;
		}
	}
	
	/**
	 * get last transaction
	 * 
	 */
	
	function getLastTransaction($order_id) {
		$sql = 'SELECT * FROM ecommerce_transaction WHERE order_id = ? ORDER BY created DESC;';
		$rs = $this->db->GetRow($sql, array((int)$order_id));
		return $rs;
	}
    
    /**
     * get order detail
     */
     
    function getOrderDetail($order_id) {
    	require_once('models/ecommerce/ecommerce_order.php');
    	$Order = new ecommerce_order();
    	$Order->setCacheable(false);
    	$order_data = $Order->getOrder($order_id);
    	//$order_data['basket']['total'] = $order_data['basket']['total'] + $order_data['basket']['delivery']['value'];
    	return $order_data;
    }
    
    /**
     * get payment type
     */
    
    function getPaymentTypeForOrder($order_id) {
    	require_once('models/ecommerce/ecommerce_order.php');
    	$Order = new ecommerce_order();
    	$Order->setCacheable(false);
    	$order_data = $Order->detail($order_id);
    	
    	return $order_data['payment_type'];
    }
    
    /**
	 * check order status
	 * process payment method only if status = 0 unpaid or 5 failed payment 
	 */
	
	function checkOrderStatusValidForPayment($status) {
		require_once('models/ecommerce/ecommerce_order.php');
		$Order = new ecommerce_order();
		
		return $Order->checkOrderStatusValidForPayment($status);
	}
}
