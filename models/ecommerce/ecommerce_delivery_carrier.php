<?php
/**
 * class ecommerce_delivery_carrier
 *
 * Copyright (c) 2009-2012 Laposa Ltd (http://laposa.co.uk)
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
	public $limit_list_countries;

	/**
	 * @access private
	 */
	public $limit_list_products;

	/**
	 * @access private
	 */
	public $limit_list_product_types;

	/**
	 * @access private
	 */
	public $limit_order_value;

	/**
	 * @access private
	 */
	public $fixed_value;

	public $fixed_percentage;
	
	/**
	 * @access private
	 */
	public $priority;

	/**
	 * @access private
	 */
	public $publish;
	
	/**
	 * serialized array country_id(int):value(float)
	 * e.g. a:1:{i:222;i:40;} for uk (222) over 40 currency units
	 * (which means that order with value over 40 delivered to country 222 will have free delivery)
	 * country_id 0 means any country, e.g. a:1:{i:0;i:40;} 
	 * (free delivery to any country for order value more than 40)
	 * 
	 */
	var $free_delivery_map;

	public $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'limit_list_countries'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'limit_list_products'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'limit_list_product_types'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'limit_order_value'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'fixed_value'=>array('label' => '', 'validation'=>'decimal', 'required'=>false),
		'fixed_percentage'=>array('label' => '', 'validation'=>'decimal', 'required'=>false),
		'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'free_delivery_map'=>array('label' => '', 'validation'=>'serialized', 'required'=>false)
		);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_delivery_carrier (
    id serial NOT NULL PRIMARY KEY,
    title varchar(255) ,
    description text ,
    limit_list_countries text ,
    limit_list_products text ,
    limit_list_product_types text ,
    limit_order_value decimal(12,5) NOT NULL DEFAULT 0,
    fixed_value decimal(12,5) NOT NULL DEFAULT 0,
	fixed_percentage decimal(5,2) NOT NULL DEFAULT 0,
    priority smallint NOT NULL DEFAULT 0,
    publish smallint NOT NULL DEFAULT 1
);
		";
		
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

	function getDetail($id) {
	
		$detail = $this->detail($id);
		$detail['free_delivery_map'] = unserialize($detail['free_delivery_map']);
		if ($detail['limit_list_countries']) $detail['limit_list_countries'] = explode(',', $detail['limit_list_countries']);
		
		return $detail;
	}
	
	/**
	 * get list
	 */

	function getList($where = '', $order = 'id ASC', $limit = '') {
	
		$list = $this->listing($where, $order, $limit);
		
		foreach ($list as $k=>$item) {
			$list[$k]['free_delivery_map'] = unserialize($item['free_delivery_map']);
			if ($item['limit_list_countries']) $list[$k]['limit_list_countries'] = explode(',', $item['limit_list_countries']);
		}
		
		return $list;
	}

		
	/**
	 * calculate delivery
	 *
	 * @param unknown_type $address_id
	 * @param unknown_type $weight
	 * @param unknown_type $goods_net
	 * @param unknown_type $carrier_id
	 * @return unknown
	 */
	
	function calculate($address_id, $weight, $goods_net, $carrier_id, $promotion_detail = false) {

		if (!is_numeric($address_id)) return false;
		if (!is_numeric($weight)) return false;
		if (!is_numeric($goods_net)) return false;
		if (!is_numeric($carrier_id)) return false;
		
		
		/**
		 * check eligibility for free delivery
		 */
		 
		if ($weight == 0) {
			$price = 0;
		} else if ($this->checkForFreeDelivery($carrier_id, $goods_net, $address_id, $promotion_detail)) {
			$price = 0;
		} else {

			/**
			 * get carrier detail
			 */
			 
			$carrier_detail = $this->getDetail($carrier_id);
			
			/**
			 * determine how to calculate
			 */
			 
			if ($carrier_detail['fixed_value'] > 0) {
				$price = $carrier_detail['fixed_value'];
			} else {
				$price = $this->calculateWeightZoneBasedBand($address_id, $weight, $carrier_id);
			}
		}
	
		/**
		 * round
		 */
		 
		$price = round($price, 2);
		
		return $price;
	}
	
	/**
	 * get address detail
	 */
	 
	function getAddressDetail($id) {
	
		if (!is_numeric($id)) {
			msg("ecommerce_delivery.getAddressDetail(): id is not numeric $id", 'error');
			return false;
		}
		
		require_once('models/client/client_address.php');
		$Address = new client_address();
		
		if ($address_detail = $Address->detail($id)) return $address_detail;
		else return false;
	}
	
	/**
	 * percentage based calc
	 */
	 
	function calculatePercentageBased($address_id, $weight, $carrier_id) {
	
		//percentage based
		/*
		$basket_detail['delivery'] = round($total * 0.03, 2);
		if ($basket_detail['delivery'] < 5) $basket_detail['delivery'] = 5;
		*/
	}
	
	/**
	 * calculate weight zone based
	 */
	
	function calculateWeightZoneBasedBand($address_id, $weight, $carrier_id) {
		
		/**
		 * check input
		 */
		 
		if (!is_numeric($address_id)) return false;
		if (!is_numeric($weight)) return false;
		if (!is_numeric($carrier_id)) return false;
		
		/**
		 * get address detail
		 */
		 
		$client_address_detail = $this->getAddressDetail($address_id);
		
		
		//get country id

		if (is_numeric($client_address_detail['country_id'])) {
			$country_id = $client_address_detail['country_id'];
		} else {
			msg("ecommerce_delivery: country_id is not numeric", 'error');
			return false;
		}

		// get price from zone/weight tables
		$sql = "
		SELECT zp.weight, zp.price FROM ecommerce_delivery_carrier_zone_to_country ztc
		LEFT OUTER JOIN ecommerce_delivery_carrier_zone_price zp ON (ztc.zone_id = zp.zone_id)
		INNER JOIN ecommerce_delivery_carrier_zone AS cz ON cz.carrier_id = $carrier_id AND cz.id = ztc.zone_id
		WHERE ztc.country_id = $country_id
		ORDER BY zp.weight ASC;
		";

		if ($zone_price = $this->executeSql($sql)) {
		
			/**
			 * identify how to calculate
			 */
			 
			if (count($zone_price) == 2) {
				
				if (($zone_price[0]['weight'] == 0 && $zone_price[1]['weight'] == 1)) {
					//calculate = base + price_per_kg * weight
					$price = $zone_price[0]['price'] + $zone_price[1]['price'] * $weight / 1000;
				} else {
					//find highest value just under the weight
					$price = $this->findHighestPriceToWeight($zone_price, $weight);
				}
			} else {
				//find highest value just under the weight
				$price = $this->findHighestPriceToWeight($zone_price, $weight);
			}
		}
		
		/**
		 * make check
		 */
		 
		if (is_numeric($price)) {
			return $price;
		} else {
			msg('ecommerce_delivery_carrier: failed to find price', 'error');
			return false;
		}

	}
	
	/**
	 * find highest value in zone price table
	 */
	 
	public function findHighestPriceToWeight($zone_price, $weight) {
		
		if (!is_array($zone_price)) return false;
		if (!is_numeric($weight)) return false;
		
		foreach ($zone_price as $item) {
			if ($item['weight'] <= $weight) $price = $item['price'];
		}

		return $price;
	}
	
	/**
	 * check eligibility for free delivery with selected carrier
	 */
	 
	public function checkForFreeDelivery($carrier_id, $goods_net, $address_id, $promotion_detail = false) {
		
		$address_detail = $this->getAddressDetail($address_id); 

		$carrier_detail = $this->getDetail($carrier_id);
		
		/**
		 * check on promotion free delivery (optionally assigned to a carrier and country)
		 */
		 
		if (is_array($promotion_detail)) {
		
			if ($promotion_detail['discount_free_delivery'] == 1) {
				
				if (
					($promotion_detail['limit_delivery_country_id'] == $address_detail['country_id'] && $promotion_detail['limit_delivery_carrier_id'] == $carrier_id) ||
					($promotion_detail['limit_delivery_country_id'] == $address_detail['country_id'] && $promotion_detail['limit_delivery_carrier_id'] == 0) ||
					($promotion_detail['limit_delivery_country_id'] == 0 && $promotion_detail['limit_delivery_carrier_id'] == $carrier_id) ||
					($promotion_detail['limit_delivery_country_id'] == 0 && $promotion_detail['limit_delivery_carrier_id'] == 0)
					) {
					
						return true;
				}
			}
		}

		/**
		 * check on carrier free delivery map
		 */
		 
		if (is_array($carrier_detail['free_delivery_map']) && count($carrier_detail['free_delivery_map']) > 0) {
			
			/**
			 * find country specific
			 */
			 
			$free_delivery_value = $carrier_detail['free_delivery_map'][$address_detail['country_id']];
			
			/**
			 * if not found try any country value (country_id = 0)
			 */
			
			if (!is_numeric($free_delivery_value)) {
			
				$free_delivery_value = $carrier_detail['free_delivery_map'][0];
			
			}
			
			/**
			 * when $free_delivery_value for specific country (or any country) is available, 
			 * check against good_net
			 */
			 
			if (is_numeric($free_delivery_value)) {
			
				if ($goods_net > $free_delivery_value) {
			
					msg("Free delivery, because it's over " . $free_delivery_value . GLOBAL_DEFAULT_CURRENCY, 'ok', 2);
					return true;
			
				} else {
			
					$diff = $free_delivery_value - $goods_net;
					$diff = money_format('%n', $diff);
					msg("Spend another $diff and your delivery using {$carrier_detail['title']} will be free!", 'ok', 1);
			
					return false;
			
				}
			
			} else {
			
				return false;
			
			}
			
		} else {
			
			return false;
		
		}
	}

}
