<?php
/**
 * class ecommerce_delivery_carrier_zone_to_country
 *
 * Copyright (c) 2009-2011 Onxshop Ltd (https://onxshop.com)
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
	
		$sql = "CREATE TABLE ecommerce_delivery_carrier_zone_to_country (
    		id serial PRIMARY KEY,
    		country_id int NOT NULL REFERENCES international_country ON UPDATE CASCADE ON DELETE CASCADE,
    		zone_id int NOT NULL REFERENCES ecommerce_delivery_carrier_zone ON UPDATE CASCADE ON DELETE CASCADE
		);
		ALTER TABLE ecommerce_delivery_carrier_zone_to_country ADD CONSTRAINT country_id_zone_id_key UNIQUE (country_id, zone_id);
		";
		
		return $sql;
	}

	/**
	 * Update zone to country connections
	 * @param  int   $zone_id     Zone to update
	 * @param  Array $country_ids Array of country ids to set
	 */
	public function batchUpdate($zone_id, $country_ids)
	{
		if (!is_numeric($zone_id)) return false;
		if (!is_array($country_ids)) return false;
		foreach ($country_ids as $country_id) if (!is_numeric($country_id)) return false;

		// insert connections that are not in the table yet
		$listing = $this->listing("zone_id = $zone_id");

		foreach ($country_ids as $country_id) {

			$insert = true;

			foreach ($listing as $item) {
				if ($item['country_id'] == $country_id) $insert = false;
			}

			if ($insert) $this->insert(array(
				"zone_id" => $zone_id,
				"country_id" => $country_id
			));
		}

		// delete rest
		if (count($country_ids) > 0) {
			$ids = implode(",", $country_ids);
			$sql = "DELETE FROM ecommerce_delivery_carrier_zone_to_country WHERE zone_id = $zone_id AND country_id NOT IN ($ids)";
		} else {
			$sql = "DELETE FROM ecommerce_delivery_carrier_zone_to_country WHERE zone_id = $zone_id";
		}
		$this->executeSql($sql);

	}	
}
