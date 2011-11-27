<?php
/**
 * class ecommerce_delivery_carrier_zone
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_delivery_carrier_zone extends Onxshop_Model {

	/**
	 * PRIMARY KEY
	 * @access private
	 */
	public $id;
	/**
	 * @access private
	 */
	public $name;

	public $carrier_id;

	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'name'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'carrier_id'=>array('label' => '', 'validation'=>'int', 'required'=>true)
		
		);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_delivery_carrier_zone (
    id serial PRIMARY KEY,
    name varchar(255),
    carrier_id integer NOT NULL DEFAULT 1 REFERENCES ecommerce_delivery_carrier ON UPDATE CASCADE ON DELETE CASCADE;
);
		";
		
		return $sql;
	}
	
}
