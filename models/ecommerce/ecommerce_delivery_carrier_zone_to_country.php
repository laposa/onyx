<?php
/**
 * class ecommerce_delivery_carrier_zone_to_country
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_delivery_carrier_zone_to_country extends Onxshop_Model {

	/**
	 * PRIMARY KEY
	 * @access private
	 */
	public $id;
	/**
	 * NOT NULL REFERENCES international_country ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	public $country_id;
	/**
	 * NOT NULL REFERENCES shipping_WZ_zone ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	public $zone_id;


	public $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'country_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'zone_id'=>array('label' => '', 'validation'=>'int', 'required'=>true)
		
		);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_delivery_carrier_zone_to_country (
    id serial PRIMARY KEY,
    country_id int NOT NULL REFERENCES international_country ON UPDATE CASCADE ON DELETE CASCADE,
    zone_id int NOT NULL REFERENCES ecommerce_delivery_carrier_zone ON UPDATE CASCADE ON DELETE CASCADE
);

ALTER TABLE ecommerce_delivery_carrier_zone_to_country ADD CONSTRAINT country_id_zone_id_key UNIQUE (country_id, zone_id);
		";
		
		return $sql;
	}
	
}
