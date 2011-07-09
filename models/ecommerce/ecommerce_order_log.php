<?php
/**
 * class ecommerce_order_log
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_order_log extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;
	/**
	 * @access private
	 */
	var $order_id;
	/**
	 * @access private
	 */
	var $status;
	/**
	 * @access private
	 */
	var $datetime;

	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'order_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'status'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'datetime'=>array('label' => '', 'validation'=>'datetime', 'required'=>true)
		);

	/**
	 * get log
	 */
	 
	function getLog($order_id) {
		if (is_numeric($order_id)) {
			$logs = $this->listing("order_id = $order_id");
			
			/*
			foreach ($logs as $log) {
				$l[$log['status']] = $log['datetime'];
			}
			return $l;
			*/
			return $logs;
		} else {
			msg("Wrong order_id ($order_id)", 'error', 1);
		}
	}
}
