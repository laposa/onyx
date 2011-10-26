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
	
	var $discount_net;
	
	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'note'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'ip_address'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'discount_net'=>array('label' => '', 'validation'=>'decimal', 'required'=>false)
		);
		
	/**
	 * get detail
	 * this is actually getFullDetail
	 * TODO: refactoring, rename to getFullDetail
	 */
	 
	function getDetail($id) {
	
		if (is_numeric($id)) {
			$basket_detail = $this->detail($id);
			$basket_detail['content'] = $this->getContent($id);
	
			return $basket_detail;
		} else {
			return false;
		}
	}
	
	/**
	 * get content
	 */

	function getContent($basket_id, $currency_code = GLOBAL_DEFAULT_CURRENCY) {
		
		if (!is_numeric($basket_id)) {
			msg("ecommerce_basket.getContent: basket_id is not numeric");
			return false;
		}
		
		require_once('models/ecommerce/ecommerce_price.php');
		$price_conf = ecommerce_price::initConfiguration();
		
		require_once('models/ecommerce/ecommerce_basket_content.php');
		require_once('models/ecommerce/ecommerce_product.php');
		
		
		$Basket_content = new ecommerce_basket_content();
		$Basket_content->setCacheable(false);
		$Product = new ecommerce_product();
		
		//get basket detail
		$basket_detail = $this->detail($basket_id);
		//convert discount value to selected currency
		$basket_detail['discount_net'] = ecommerce_price::convertCurrency($basket_detail['discount_net'], GLOBAL_DEFAULT_CURRENCY, $currency_code);
		//get detail of items in basket
		$basket_content_data = $Basket_content->getItems($basket_detail['id']);

		$total_items_qty = 0;
		
		if ($basket_content_data) {
		
			foreach ($basket_content_data as $basket_key=>$basket_item) {
			
				$variety_detail = $Product->getProductVarietyDetail($basket_item['product_variety_id'], $basket_item['price_id'], $currency_code);
				$product_detail = $Product->ProductDetail($variety_detail['product_id']);
				
				//find product in the node
				$node_detail = $Product->findProductInNode($product_detail['id']);
				$node_detail = $node_detail[0];

				if ($basket_item['quantity'] > $variety_detail['stock'] && $variety_detail['stock'] > 0) {
					msg("Sorry, we have not enough items on the stock for {$product_detail['name']} - {$variety_detail['name']}", 'error', 2);
				}
				

				$price = $variety_detail['price'];
				
				$basket_item['price'] = $price['value'];
				$basket_item['total'] = $price['value'] * $basket_item['quantity'];
				$basket_item['total_net'] = $price['value_net'] * $basket_item['quantity'];
				$basket_item['vat'] = $product_detail['vat'] * $price['value_net']/100 * $basket_item['quantity'];
				$basket_item['vat'] = round($basket_item['vat'], 5);
				if (($price_conf['backoffice_with_vat'] && ONXSHOP_IN_BACKOFFICE) || ($price_conf['frontend_with_vat'] && !ONXSHOP_IN_BACKOFFICE)) {
					$basket_item['total_inc_vat'] = $basket_item['total'];
				} else {
					$basket_item['total_inc_vat'] = $basket_item['total'] + $basket_item['vat'];
				}

				$total = $total + $basket_item['total'];
				$total_goods_net = $total_goods_net + $basket_item['total_net'];
				$total_weight = $total_weight + $variety_detail['weight'] * $basket_item['quantity'];
				//gross weight for delivery purposes 
				$total_weight_gross = $total_weight_gross + $variety_detail['weight_gross'] * $basket_item['quantity'];
				
				$total_vat = $total_vat + $basket_item['vat'];
				
				//other data
				$basket_item['other_data'] = unserialize($basket_item['other_data']);
				
				$items[$basket_key] = $basket_item;
				
				$product_detail['variety'] = $variety_detail;
				$product_detail['node'] = $node_detail;
				$items[$basket_key]['product'] = $product_detail;
				$total_items_qty = $total_items_qty + $basket_item['quantity'];
			}
		}
		$basket_detail['total_sub'] = $total - $basket_detail['discount_net'];

		$basket_detail['total_weight'] = $total_weight;
		$basket_detail['total_weight_gross'] = $total_weight_gross;
		$basket_detail['total_vat'] = $total_vat;
		if (($price_conf['backoffice_with_vat'] && ONXSHOP_IN_BACKOFFICE) || ($price_conf['frontend_with_vat'] && !ONXSHOP_IN_BACKOFFICE)) {
			$basket_detail['total'] = $basket_detail['total_sub'];
		} else {
			$basket_detail['total'] = $basket_detail['total_sub'] + $basket_detail['total_vat'];			
		}
		
		$basket_detail['total_goods_net'] = $total_goods_net - $basket_detail['discount_net'];
		$basket_detail['total_goods_net_before_discount'] = $total_goods_net;
		
		if ($basket_detail['total_goods_net'] < 0) {
			$basket_detail['total_goods_net'] = 0;
			msg('Eligible discount is bigger than order value');
			//return false;
		}
		
		$basket = $basket_detail;
		if (!is_array($items)) $items = array();
		$basket['items'] = $items;
		$basket['total_items'] = count($basket['items']);
		$basket['total_items_qty'] = $total_items_qty;
		//print_r($basket);

		return $basket;
	}

	/**
	 * add to basket
	 *
	 *
	 * @param int product_variety_id 	 * @param int quantity 	 * @return bool
	 * @access public
	 */
	 
	function addToBasket($basket_id, $product_variety_id,  $quantity = 1, $other_data = array() ) {

		/**
		 * get product info
		 */
		 
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		$product_data = $Product->getProductDetailByVarietyId($product_variety_id);
		$price_id = $product_data['variety']['price']['id'];
		$product_type_id = $product_data['product_type_id'];
		
		/**
		 * get detail for current basket
		 */
		 
		$basket_content_data_current = $this->getContent($basket_id);
		
		foreach ($basket_content_data_current['items'] as $current) {
			//if the same variety_id, price_id and other_data, than do an update instead
			if  ($current['product_variety_id'] == $product_variety_id && $current['price_id'] == $price_id && $current['other_data'] == $other_data) {
				if ($this->updateBasketContent($basket_id, $current['id'], $current['quantity'] + $quantity)) {
					msg("ecommerce_basket.addToBasket: Item in basket has been updated", 'ok', 2);
					return true;
				}
			}
		}
		
		/**
		 * or insert as a new item
		 */
			
		$basket_content_data = array('basket_id'=>$basket_id, 'product_variety_id'=>$product_variety_id, 'quantity'=>$quantity, 'price_id'=>$price_id, 'other_data'=>$other_data, 'product_type_id'=>$product_type_id);
		
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
		
		$basket_data = $Basket_content->detail($basket_content_id);
		$basket_data['quantity'] = $quantity;
		
		if ($Basket_content->update($basket_data)) {
			return true;
		} else {
			return false;
		}
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
	 * Discount
	 */

	function applyDiscount($basket_id, $discount_net) {
	
		if (!is_numeric($basket_id)) return false;
		if (!is_numeric($discount_net)) return false;
		
		//msg("Setting discount $discount_net");
		$basket_data = $this->detail($basket_id);
		$basket_data['discount_net'] = $discount_net;
		
		if ($this->update($basket_data)) {
			return true;
		} else {
			return false;
		}
		
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
			
			if ($basket_detail = $this->getDetail($order_detail['basket_id'])) {
				return $basket_detail;
			} else {
				return false;
			}
			
		} else {
			return false;
		}
	}
	
	/**
	 * calculateDelivery
	 */
	 
	function calculateDelivery($basket_id, $delivery_address_id, $delivery_options, $promotion_code = false) {
		
		//get basket content
		$basket_content = $this->getContent($basket_id);
		
		//find promotion data for delivery calculation
		if ($promotion_code) {
			require_once('models/ecommerce/ecommerce_promotion.php');
			$Promotion = new ecommerce_promotion();
			$customer_id = $basket_content['customer_id'];
			$promotion_data = $Promotion->checkCodeBeforeApply($promotion_code, $customer_id);
		} else {
			$promotion_data = false;
		}
		
		//calculate delivery
		require_once('models/ecommerce/ecommerce_delivery.php');
		$Ecommerce_Delivery = new ecommerce_delivery();
		$delivery = $Ecommerce_Delivery->calculateDelivery($basket_content, $delivery_address_id, $delivery_options, $promotion_data);
		
		return $delivery;
		
	}
	
}
