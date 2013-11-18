<?php
/**
 * class ecommerce_delivery_carrier_rate
 *
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_delivery_carrier_rate extends Onxshop_Model {

	/**
	 * @access private
	 */
	public $id;

	/**
	 * @access private
	 */
	public $carrier_id;

	/**
	 * @access private
	 */
	public $weight_from;

	/**
	 * @access private
	 */
	public $weight_to;

	/**
	 * @access private
	 */
	public $price;

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required' => true), 
		'carrier_id' => array('label' => '', 'validation' => 'int', 'required' => true),
		'weight_from' => array('label' => '', 'validation' => 'decimal', 'required' => false),
		'weight_to' => array('label' => '', 'validation' => 'decimal', 'required' => false),
		'price' => array('label' => '', 'validation' => 'decimal', 'required' => true)
	);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "CREATE TABLE ecommerce_delivery_carrier_rate (
			id serial PRIMARY KEY NOT NULL,
			carrier_id int REFERENCES ecommerce_delivery_carrier ON UPDATE CASCADE ON DELETE CASCADE,
			weight_from numeric(12,5) DEFAULT 0,
			weight_to numeric(12,5) DEFAULT 0,
			price numeric(12,5)
		);

		CREATE INDEX ecommerce_delivery_carrier_rate_carrier_id_idx ON ecommerce_delivery_carrier_rate USING btree (carrier_id);
		CREATE INDEX ecommerce_delivery_carrier_rate_weight_idx ON ecommerce_delivery_carrier_rate USING btree (weight_from, weight_to);
		";		

		return $sql;
	}
	
}