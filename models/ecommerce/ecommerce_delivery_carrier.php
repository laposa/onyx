<?php
/**
 * class ecommerce_delivery_carrier
 *
 * Copyright (c) 2009-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_delivery_carrier extends Onxshop_Model {

	/**
	 * @access private
	 */
	public $id;

	/**
	 * DHL48, DHL24, Roayal Mail, UPS
	 * @access private
	 */
	public $title;

	/**
	 * @access private
	 */
	public $description;

	/**
	 * @access private
	 */
	public $priority;

	/**
	 * @access private
	 */
	public $publish;

	/**
	 * @access private
	 */
	public $zone_id;

	/**
	 * @access private
	 */
	public $order_value_from;

	/**
	 * @access private
	 */
	public $order_value_to;

	/**
	 * @access private
	 */
	public $warehouse_id;
	
	public $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'zone_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'order_value_from'=>array('label' => '', 'validation'=>'float', 'required'=>false),
		'order_value_to'=>array('label' => '', 'validation'=>'float', 'required'=>false),
		'warehouse_id'=>array('label' => '', 'validation'=>'int', 'required'=>false)
	);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "CREATE TABLE ecommerce_delivery_carrier (
		    id serial NOT NULL PRIMARY KEY,
		    title varchar(255) ,
		    description text ,
		    priority smallint NOT NULL DEFAULT 0,
		    publish smallint NOT NULL DEFAULT 1,
			zone_id integer NOT NULL REFERENCES ecommerce_delivery_carrier_zone(id) ON UPDATE CASCADE ON DELETE CASCADE,
			order_value_from numeric(12,5),
			order_value_to numeric(12,5)
		)";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
	
		if (array_key_exists('ecommerce_delivery_carrier', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_delivery_carrier'];
		else $conf = array();
	
		if (!is_numeric($conf['default_carrier_id'])) $conf['default_carrier_id'] = 1; //Standard - fixed value
		
		return $conf;
	}
	
	/**
	 * get detail
	 */

	function getDetail($id)
	{
		$detail = $this->detail($id);
		return $detail;

	}
	
	/**
	 * get list
	 */

	function getList($where = '', $order = 'id ASC', $limit = '')
	{
		$list = $this->listing($where, $order, $limit);
		return $list;
	}

	/**
	 * Returns delivery rate according to given order value and weight
	 * 
	 * @param  int   $carrier_id  Carrier Id
	 * @param  float $order_value Order value ($basket['sub_total']['price'])
	 * @param  float $weight      Order weight ($basket['total_weight_gross'])
	 * @return bool|float         Delivery rate (excl. VAT), which can be zero (= free delivery), or false, which
	 *                            indicates given method cannot be used with given order value and weight
	 */
	function getDeliveryRate($carrier_id, $order_value, $weight)
	{
		if (!is_numeric($carrier_id)) return false;
		if (!is_numeric($order_value)) return false;
		if (!is_numeric($weight)) return false;

		// zero weight means free delivery
		if ($weight == 0) return 0;

		// convert weight units
		require_once('models/ecommerce/ecommerce_product_variety.php');
		$product_variety_conf = ecommerce_product_variety::initConfiguration();
		$Variety = new ecommerce_product_variety();
		$weight = $Variety->convertWeight($weight, $product_variety_conf['weight_units'], 'g');

		// check order value
		$carrier = $this->getDetail($carrier_id);
		if ($order_value < $carrier['order_value_from'] || $order_value >= $carrier['order_value_to']) return false;

		// check weight
		require_once('models/ecommerce/ecommerce_delivery_carrier_rate.php');
		$Rate = new ecommerce_delivery_carrier_rate();
		$rates = $Rate->listing("carrier_id = $carrier_id AND weight_from <= $weight AND weight_to > $weight");

		if (count($rates) == 0) return false;
		if (!isset($rates[0]['price'])) return false;

		return $rates[0]['price'];
	}

}
