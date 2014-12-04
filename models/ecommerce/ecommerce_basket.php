<?php
/**
 * class ecommerce_basket
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_basket extends Onxshop_Model {

	/**
	 * @access public
	 */
	var $id;
	/**
	 * REFERENCES customer(id) ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	var $customer_id;
	/**
	 * @access private
	 */
	var $created;
	/**
	 * @access private
	 */
	var $note;
	/**
	 * @access private
	 */
	var $ip_address;
	
	var $face_value_voucher;
	
	var $title;
	
	var $other_data;
	
	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'note'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'ip_address'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'face_value_voucher'=>array('label' => '', 'validation'=>'decimal', 'required'=>false),
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false)
		);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "CREATE TABLE ecommerce_basket (
		    id serial NOT NULL PRIMARY KEY,
		    customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
		    created timestamp(0) without time zone,
		    note text,
		    ip_address character varying(255),
		    face_value_voucher decimal(12,5),
		    title character varying(255),
		    other_data	text
		);
		";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
	
		if (array_key_exists('ecommerce_basket', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_basket'];
		else $conf = array();
		
		return $conf;
	}


	/**
	 * get detail
	 */
	 
	function getDetail($id) {
	
		if (is_numeric($id)) return $this->detail($id);
		return false;
	}

	/**
	 * get full detail including items
	 */
	 
	function getFullDetail($id, $currency = GLOBAL_DEFAULT_CURRENCY) {
	
		if (is_numeric($id)) {
			$basket = $this->detail($id);

			require_once('models/ecommerce/ecommerce_basket_content.php');
			$Basket_content = new ecommerce_basket_content();
			$Basket_content->setCacheable(false);
			$basket['items'] = $Basket_content->getItems($id);
			$basket['currency'] = $currency;

			require_once('models/ecommerce/ecommerce_product.php');
			$Product = new ecommerce_product();
			$Product->setCacheable(false);

			foreach ($basket['items'] as &$item) {

				$variety = $Product->getProductVarietyDetail($item['product_variety_id'], $item['price_id'], $currency);
				$item['product'] = $Product->ProductDetail($variety['product_id']);
				$node = $Product->findProductInNode($item['product']['id']);
				$item['product']['variety'] = $variety;
				$item['product']['node'] = $node[0];
				$item['other_data'] = unserialize($item['other_data']);

			}

			return $basket;

		} else {

			return false;

		}
	}

	/**
	 * calculate sub totals
	 * requires basket full detail
	 */
	public function calculateBasketSubTotals(&$basket, $include_vat = true)
	{
		$basket['sub_total']['price'] = 0;

		foreach ($basket['items'] as &$item) {

			$item['unit_price'] = $include_vat ? $item['product']['variety']['price']['value'] : $item['product']['variety']['price']['value_net'];
			$item['vat_rate'] = $include_vat ? $item['product']['variety']['type']['vat'] : 0;
			$item['price'] = $item['unit_price'] * $item['quantity'];

			$basket['total_weight'] += $item['product']['variety']['weight'] * $item['quantity'];
			$basket['total_weight_gross'] += $item['product']['variety']['weight_gross'] * $item['quantity'];

			$basket['quantity'] += $item['quantity'];
			$basket['count']++;

			$basket['sub_total']['price'] += $item['price'];
			$basket['sub_totals'][$item['vat_rate']]['price'] += $item['price'];
			$basket['sub_totals'][$item['vat_rate']]['quantity'] += $item['quantity'];

		}
	}

	/**
	 * calculate basket totals
	 * requires basket full detail with calculated sub totals, applied discount and calculated delivery
	 */
	public function calculateBasketTotals(&$basket)
	{
		foreach ($basket['items'] as &$item) {
			$item['total'] = $item['price'] - $item['discount'];			
			$item['vat'] = $item['total'] / (100 + $item['vat_rate']) * $item['vat_rate'];
			$item['net'] = $item['total'] / (100 + $item['vat_rate']) * 100;
			$basket['sub_total']['vat'] += $item['vat'];
			$basket['sub_total']['net'] += $item['net'];
			$basket['sub_totals'][$item['vat_rate']]['total'] += $item['total'];
			$basket['sub_totals'][$item['vat_rate']]['vat'] += $item['vat'];
			$basket['sub_totals'][$item['vat_rate']]['net'] += $item['net'];

		}

		$basket['total_net'] = $basket['sub_total']['net'] + $basket['delivery']['value_net'];
		$basket['total_vat'] = $basket['sub_total']['vat'] + $basket['delivery']['vat'];
		$basket['total'] = max(0, $basket['sub_total']['price'] - $basket['face_value_voucher'] - $basket['discount']) + $basket['delivery']['value'];
	}

	/**
	 * apply discount code
	 * requires basket full detail with calculated sub totals
	 */
	public function calculateBasketDiscount(&$basket, $code, $check_code = true)
	{
		$promotion_data = false;

		$basket['face_value_voucher'] = 0;
		$basket['face_value_voucher_claim'] = 0;
		$basket['discount'] = 0;
		$basket['discount_fixed_claim'] = 0;
		$basket['discount_percentage_claim'] = 0;
		foreach ($basket['items'] as &$item) $item['discount'] = 0;

		if ($code) {

			require_once('models/ecommerce/ecommerce_promotion.php');
			$Promotion = new ecommerce_promotion();
			$Promotion->setCacheable(false);

			if ($check_code) $promotion_data = $Promotion->checkCodeBeforeApply($code, $basket['customer_id'], $basket);
			else $promotion_data = $Promotion->checkCodeMatch($code);

			if ($promotion_data) {

				$promotion_data['discount_fixed_value'] = ecommerce_price::convertCurrency($promotion_data['discount_fixed_value'], 
					GLOBAL_DEFAULT_CURRENCY, $basket['currency']);

				if ($promotion_data['type']['taxable']) { 

					$this->calculateVoucherDiscount($basket, $promotion_data);

				} else {

					$this->calculateCouponDiscount($basket, $promotion_data);

				}

			}

		}

		return $promotion_data;

	}

	/**
	 * Calculate Gift Voucher Discount (taxable)
	 */
	protected function calculateVoucherDiscount(&$basket, &$promotion_data)
	{
		// gift Vouchers
		$basket['face_value_voucher'] = min($basket['sub_total']['price'], $promotion_data['discount_fixed_value']);
		$basket['face_value_voucher_claim'] = $promotion_data['discount_fixed_value'];
	}			

	/**
	 * Calculate Gift Voucher Discount (taxable)
	 */
	protected function calculateCouponDiscount(&$basket, &$promotion_data)
	{
		// check for promotion limit for certain products
		if (strlen($promotion_data['limit_list_products']) > 0) $limited_ids = explode(",", $promotion_data['limit_list_products']);
		else $limited_ids = false;

		if ($promotion_data['discount_fixed_value'] > 0) {

			// if discount is limited to certain products the discount will be applied only to part of the order
			if ($limited_ids && count($basket['items']) > 0) {
				foreach ($basket['items'] as $item) {
					if (in_array($item['product']['id'], $limited_ids)) $discount_value += (float) $item['price']; 
				}
			} else {
				$discount_value = $basket['sub_total']['price'];
			}
			// make sure fixed discount does not exceed the value of the order
			$factor = min($discount_value, $promotion_data['discount_fixed_value']) / $discount_value;

		} else {
			$factor = $promotion_data['discount_percentage_value'] / 100;
		}

		// apply discount to each item
		foreach ($basket['items'] as &$item) {

			// skip items if promotion does not applies to them
			if ($limited_ids && !in_array($item['product']['id'], $limited_ids)) continue;

			$item['discount'] = $item['price'] * $factor;
			$basket['discount'] += $item['discount'];
			$basket['sub_totals'][$item['vat_rate']]['discount'] += $item['discount'];

		}

		// store claimed value
		$basket['discount_fixed_claim'] = $promotion_data['discount_fixed_value'];
		$basket['discount_percentage_claim'] = $promotion_data['discount_percentage_value'];
	}

	/**
	 * Save calculated discount to table
	 */
	public function saveDiscount(&$basket)
	{
		// save face value
		$this->update(array('id' => $basket['id'], 'face_value_voucher' => $basket['face_value_voucher']));

		// save discounts
		require_once('models/ecommerce/ecommerce_basket_content.php');
		$Basket_content = new ecommerce_basket_content();
		
		foreach ($basket['items'] as &$item) {
			$Basket_content->update(array('id' => $item['id'], 'discount' => $item['discount']));
		}
	}

	/**
	 * add to basket
	 *
	 * @param int basket_id
	 * @param int product_variety_id
	 * @param int quantity
	 * @param array other_data
	 * @return bool
	 * @access public
	 */
	 
	function addToBasket($basket_id, $product_variety_id,  $quantity = 1, $other_data = array(), $price_id = false) {
		
		// get product info
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		$product_data = $Product->getProductDetailByVarietyId($product_variety_id);
		if (!is_numeric($price_id)) $price_id = $product_data['variety']['price']['id'];
		$product_type_id = $product_data['variety']['product_type_id'];

		// limit to delivery zone (if delivery address is set already)
		if (!empty($product_data['variety']['limit_to_delivery_zones']) && is_numeric($_SESSION['client']['customer']['delivery_address_id'])) {

			$zones = explode(",", $product_data['variety']['limit_to_delivery_zones']);

			if (is_array($zones)) {

				require_once('models/ecommerce/ecommerce_delivery_carrier_zone.php');
				$DeliveryZone = new ecommerce_delivery_carrier_zone();
				$delivery_zone_id = $DeliveryZone->getZoneIdByAddress($_SESSION['client']['customer']['delivery_address_id']);

				if (!in_array($delivery_zone_id, $zones)) {
					msg("Sorry, we're not able to deliver this product to your country.", 'error');
					return false;
				}

			}
		}

		// get detail for current basket
		$basket = $this->getFullDetail($basket_id);

		foreach ($basket['items'] as $item) {
			//if the same variety_id, price_id and other_data, than do an update instead
			if  ($item['product_variety_id'] == $product_variety_id && $item['price_id'] == $price_id && $item['other_data'] == $other_data) {
				if ($this->updateBasketContent($basket_id, $item['id'], $item['quantity'] + $quantity)) {
					msg("ecommerce_basket.addToBasket: Item in basket has been updated", 'ok', 2);
					return true;
				} else {
					msg("Current item {$item['id']} was found in basket $basket_id, but cannot update.", 'error', 1);
					return false;
				}
			}
		}
		
		/**
		 * or insert as a new item
		 */
			
		$basket_content_data = array(
			'basket_id' => $basket_id,
			'product_variety_id' => $product_variety_id,
			'quantity' => $quantity,
			'price_id' => $price_id,
			'other_data' => $other_data,
			'product_type_id' => $product_type_id
		);
		
		if ($this->insertItemIntoBasketContent($basket_content_data)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * insert item into basket content
	 */
	
	function insertItemIntoBasketContent($data) {
	
		require_once('models/ecommerce/ecommerce_basket_content.php');
		$Basket_content = new ecommerce_basket_content();
		
		if ($Basket_content->insertItem($data)) {
			return true;
		} else {
			return false;
		}

	}
	
	/**
	 * remove from basket
	 */
	
	function removeFromBasket($basket_id, $basket_content_id) {
	
		require_once('models/ecommerce/ecommerce_basket_content.php');
		$Basket_content = new ecommerce_basket_content();
		
		//safety check if we are removing an item related to the basket
		$basket_content_data = $Basket_content->detail($basket_content_id);
		
		if ($basket_content_data['basket_id'] != $basket_id) {
			msg("Trying to remove an item (id $basket_content_id) from a different basket (id $basket_id)", 'error');
		} else {
			if ($Basket_content->delete($basket_content_id)) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * update basket
	 */
	
	function updateBasketContent($basket_id, $basket_content_id, $quantity) {
	
		if (!is_numeric($basket_id)) return false;
		if (!is_numeric($basket_content_id)) return false;
		if (!is_numeric($quantity)) return false;
		
		require_once('models/ecommerce/ecommerce_basket_content.php');
		$Basket_content = new ecommerce_basket_content();
		
		$basket_content_data = $Basket_content->detail($basket_content_id);
		$basket_content_data['quantity'] = $quantity;

		return $Basket_content->updateItem($basket_content_data);
				
	}
	
	/**
	 * get content
	 */
	 
	function getContentItemsVarietyIdList($basket_id) {
	
		if (!is_numeric($basket_id)) {
			msg("ecommerce_basket->getContentItemsVarietyIdList(): basket_id is not numeric", 'error', 1);
			return false;
		}
		 
		require_once('models/ecommerce/ecommerce_basket_content.php');
		$Basket_content = new ecommerce_basket_content();
		$Basket_content->setCacheable(false);
		
		$basket_content_data = $Basket_content->getItems($basket_id);
		
		$id_list = array();
		
		foreach ($basket_content_data as $item) {
			$id_list[] = intval($item['product_variety_id']);
		}

		return $id_list;
	}
	
	/**
	 * get content
	 */

	function getContentItemsProductIdList($basket_id) {
	
		if (!is_numeric($basket_id)) {
			msg("ecommerce_basket->getContentItemsProductIdList(): basket_id is not numeric", 'error', 1);
			return false;
		}
		require_once('models/ecommerce/ecommerce_product_variety.php');
		$Product_variety = new ecommerce_product_variety();
		
		$variety_ids = $this->getContentItemsVarietyIdList($basket_id);

		$id_list = array();
		
		foreach ($variety_ids as $variety_id) {
			$variety_detail = $Product_variety->detail($variety_id);
			$id_list[] = intval($variety_detail['product_id']);
		}

		return $id_list;
	}

	

		
	/**
	 * delete unused baskets
	 */
	
	function deleteOrphanedAnonymouseBaskets($age = 2) {
	
		if (!(is_numeric($age) && $age > 0)) {
			msg('ecommerce_basket->deleteOrphanedAnonymouseBaskets(): Age must be numeric', 'error');
			return false;
		}
		
		$sql = "DELETE FROM ecommerce_basket WHERE customer_id = 0 AND created < NOW() - INTERVAL '$age weeks';";
		
		if ($this->executeSql($sql)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * get basket by order id
	 */
	 
	public function getBasketByOrderId($order_id) {
		
		if (!is_numeric($order_id)) return false;
		
		require_once('models/ecommerce/ecommerce_order.php');
		$Order = new ecommerce_order();
		
		if ($order_detail = $Order->getDetail($order_id)) {
			
			if ($basket_detail = $this->getFullDetail($order_detail['basket_id'])) {
				return $basket_detail;
			} else {
				return false;
			}
			
		} else {
			return false;
		}
	}
	
	/**
	 * Get customer's most recent basket that was not converted to an order yet
	 * @param  int   $customer_id Customer Id
	 * @return Array              Basket detail
	 */
	function getLastLiveBasket($customer_id)
	{
		if (!is_numeric($customer_id)) return false;

		$sql = "SELECT b.*, o.id AS order_id
			FROM ecommerce_basket AS b
			LEFT JOIN ecommerce_order AS o ON o.basket_id = b.id
			WHERE customer_id = $customer_id
			ORDER BY created DESC
			LIMIT 1 OFFSET 0";

		$records = $this->executeSql($sql);

		if (isset($records[0]) && !$records[0]['order_id']) return $records[0];
		else return false;
	}

}
