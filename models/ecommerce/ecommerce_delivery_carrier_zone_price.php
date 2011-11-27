<?php
/**
 * class shipping_wz_zone_price
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_delivery_carrier_zone_price extends Onxshop_Model {

	/**
	 * PRIMARY KEY
	 * @access private
	 */
	public $id;
	/**
	 * NOT NULL REFERENCES shipping_WZ_zone ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	public $zone_id;
	/**
	 * @access private
	 */
	public $weight;
	/**
	 * @access private
	 */
	public $price;
	/**
	 * @access private
	 */
	public $currency_code;

	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'zone_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'weight'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'price'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
		'currency_code'=>array('label' => '', 'validation'=>'string', 'required'=>true)
		
		);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_delivery_carrier_zone_price (
    id serial PRIMARY KEY,
    zone_id int NOT NULL REFERENCES ecommerce_delivery_carrier_zone ON UPDATE CASCADE ON DELETE CASCADE,
    weight int ,
    price numeric(9,2) ,
    currency_code char(3)
);
		";
		
		return $sql;
	}
	
}