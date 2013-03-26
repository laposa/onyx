<?php
/**
 * class ecommerce_delivery
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * This table will say, which shipping_type (shipping controller) is possible with
 * an product type.
 * UPS, royal mail, CityLink, email, download
 * -fixed rate (if amount > x) shipping = 0)
 * -weight and zone (WZ)
 * -size
 */
 
class ecommerce_delivery extends Onxshop_Model {

	/**
	 * PRIMARY KEY
	 * @access private
	 */
	var $id;
	/**
	 * REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT
	 * @access private
	 */
	var $order_id;
	/**
	 * 
	 * @access private
	 */
	var $carrier_id;
	/**
	 * @access private
	 */
	var $value_net;
	/**
	 * @access private
	 */
	var $vat;
	/**
	 * @access private
	 */
	var $vat_rate;
	/**
	 * @access private
	 */
	var $required_datetime;
	/**
	 * @access private
	 */
	var $note_customer;
	/**
	 * @access private
	 */
	var $note_backoffice;
	/**
	 * @access private
	 */
	var $other_data;
	
	var $weight;

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'order_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'carrier_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'value_net'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
		'vat'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
		'vat_rate'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
		'required_datetime'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'note_customer'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'note_backoffice'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'other_data'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'weight'=>array('label' => '', 'validation'=>'int', 'required'=>true)
		);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_delivery (
    id serial NOT NULL PRIMARY KEY,
    order_id int REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT,
    carrier_id integer REFERENCES ecommerce_delivery_carrier ON UPDATE CASCADE ON DELETE CASCADE ,
    value_net decimal(12,5) ,
    vat decimal(12,5) ,
    vat_rate decimal(12,5) ,
    required_datetime timestamp(0) without time zone,
    note_customer text ,
    note_backoffice text ,
    other_data text,
	weight integer NOT NULL DEFAULT 0
);
		";
		
		return $sql;
	}
	
	/**
	 * insert delivery
	 */
	 
	function insertDelivery($delivery_data) {
		$delivery_data['other_data'] = serialize($delivery_data['other_data']);

		if ($id = $this->insert($delivery_data)) return $id;
		else return false;
	}

	/**
	 * get delivery list for an order
	 */
	 
	function getDeliveryListByOrderId($order_id) {
		if (!is_numeric($order_id)) {
			msg("ecommerce_delivery.getDeliveryListByOrderId(): order_id is not numeric", 'error', 1);
			return false;
		}
		
		$list = $this->listing("order_id = $order_id");
		foreach ($list as $key=>$val) {
			$list[$key]['carrier_detail'] = $this->getCarrierDetail($val['carrier_id']);
		}
		return $list;
	}
	
	/**
	 * get delivery by order id
	 */
	 
	function getDeliveryByOrderId($order_id) {
		
		$list = $this->getDeliveryListByOrderId($order_id);
		
		$delivery = $list[0];
		$delivery['value'] = $delivery['value_net'] + $delivery['vat'];
		
		return $delivery;
	}
	
	/**
	 * get carrier detail
	 */
	 
	function getCarrierDetail($carrier_id) {
		require_once('models/ecommerce/ecommerce_delivery_carrier.php');
		$Carrier = new ecommerce_delivery_carrier();
		$detail = $Carrier->getDetail($carrier_id);
		
		return $detail;
	}

	/**
	 * calculate delivery
	 *
	 * @param unknown_type $address_id
	 * @param unknown_type $weight
	 * @param unknown_type $goods_net
	 * @param unknown_type $options
	 * @return unknown
	 */
	
	function calculate($address_id, $weight, $goods_net, $options, $promotion_detail = false) {
	
		require_once('models/ecommerce/ecommerce_delivery_carrier.php');
		$Carrier = new ecommerce_delivery_carrier();
		
		if (is_array($options)) {
			if (is_numeric($options['carrier_id'])) $carrier_id = $options['carrier_id'];
			else {
				$carrier_id = $Carrier->conf['default_carrier_id'];
			}
		} else {
			$carrier_id = $Carrier->conf['default_carrier_id'];
		}
		
		$delivery_price = $Carrier->calculate($address_id, $weight, $goods_net, $carrier_id, $promotion_detail); 
		
		return $delivery_price;
	}
	
	/**
	 * calculate delivery cost of the order
	 *
	 * @param unknown_type $basket_content
	 * @param unknown_type $delivery_address_id
	 * @param unknown_type $delivery_options
	 * @return unknown
	 */
	 
	function calculateDelivery($basket_content, $delivery_address_id, $delivery_options = false, $promotion_detail = false) {
				
		//if there is a product with vat rate > 0, add vat to the shipping
		$add_vat = $this->findVATEligibility($basket_content);

		//get weight for delivery
		$total_weight = $basket_content['total_weight_gross'];
		
		//convert total weight to grams
		require_once('models/ecommerce/ecommerce_product_variety.php');
		$product_variety_conf = ecommerce_product_variety::initConfiguration();
		$total_weight = $this->convertWeight($total_weight, $product_variety_conf['weight_units'], 'g');

		//calculate delivery
		$delivery_price = $this->calculate($delivery_address_id, $total_weight, $basket_content['total_goods_net'], $delivery_options, $promotion_detail);
	
		//assign
		$delivery['value_net'] = $delivery_price;
		$delivery['weight'] = $total_weight;
		$delivery['vat_rate'] = $add_vat;

		//format
		$delivery['value_net'] = sprintf("%0.2f", $delivery['value_net']);
		
		//add vat
		if ($add_vat > 0) {
			$delivery['vat'] = $delivery['value_net'] * $add_vat / 100;
			$delivery['value'] = $delivery['value_net'] + $delivery['vat'];
		} else {
			$delivery['vat'] = 0;
			$delivery['value'] = $delivery['value_net'];
		}
		
		return  $delivery;
	}
	
	/**
	 * If basket contains at least one VAT item, return VAT rate
	 *
	 * @param unknown_type $basket_content
	 * @return unknown
	 */
	 
	function findVATEligibility($basket_content) {
	
		if (!is_array($basket_content)) return false;
		
		foreach ($basket_content['items'] as $item) {
			if ($item['vat'] > 0) {
				$vat_rate = (string) $item['product']['vat'];
				return $vat_rate;
			}
		}
		
		return 0;
	}

	/**
	 * duplication with ./ecommerce/ecommerce_product_variety.php:
	 *
	 * @param unknown_type $weight
	 * @param unknown_type $from
	 * @param unknown_type $to
	 * @return unknown
	 */
	 
	function convertWeight($weight, $from, $to) {
			switch ($from) {
				case 't':
					$from_ref = 1000 * 1000;
				break;
				case 'kg':
					$from_ref = 1000;
				break;
				case 'g':
					$from_ref = 1;
				default:
				break;
			}
			
			switch ($to) {
				case 't':
					$to_ref = 1000 * 1000;
				break;
				case 'kg':
					$to_ref = 1000;
				break;
				case 'g':
					$to_ref = 1;
				default:
				break;
			}
			
			$weight = $from_ref * $weight / $to_ref;
			
			return $weight;
	}



}
