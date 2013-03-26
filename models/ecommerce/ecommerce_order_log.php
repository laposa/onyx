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
		0 New (unpaid)
		1 New (paid)
		2 Dispatched
		3 Complete
		4 Cancelled
		5 Failed payment
		6 In Progress
		7 Split
		
	 * @access private
	 */
	var $status;
	/**
	 * @access private
	 */
	var $datetime;

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'order_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'status'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'datetime'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false)
		);

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_order_log (
    id serial NOT NULL PRIMARY KEY,
    order_id integer REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT,
    status integer,
    datetime timestamp(0) without time zone,
    description text,
    other_data text
);
		";
		
		return $sql;
	}
	
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
