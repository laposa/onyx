<?php
/**
 * class ecommerce_promotion
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_promotion extends Onxshop_Model {

	/**
	 * @access private
	 */
	public $id;

	/**
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
	public $publish;

	/**
	 * @access private
	 */
	public $created;

	/**
	 * @access private
	 */
	public $modified;

	/**
	 * @access private
	 */
	public $customer_account_type;

	/**
	 * preg pattern
	 * @access private
	 */
	public $code_pattern;

	/**
	 * 
	 * @access private
	 */
	public $discount_fixed_value;

	/**
	 * 
	 * @access private
	 */
	public $discount_percentage_value;
	
	/**
	 * @access private
	 */
	public $discount_free_delivery;

	/**
	 * @access private
	 */
	public $uses_per_coupon;

	/**
	 * @access private
	 */
	public $uses_per_customer;

	/**
	 * @access private
	 */
	public $limit_list_products;

	/**
	 * serialized
	 * @access private
	 */
	public $other_data;

	public $limit_delivery_country_id;
	
	public $limit_delivery_carrier_id;

	public $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'customer_account_type'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'code_pattern'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'discount_fixed_value'=>array('label' => '', 'validation'=>'decimal', 'required'=>false),
		'discount_percentage_value'=>array('label' => '', 'validation'=>'decimal', 'required'=>false),
		'discount_free_delivery'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'uses_per_coupon'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'uses_per_customer'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'limit_list_products'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
		'limit_delivery_country_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'limit_delivery_carrier_id'=>array('label' => '', 'validation'=>'int', 'required'=>false)
		);
		
	/**
	 * list
	 */
		
	public function getList() {
	
		$list = $this->listing();
		
		foreach ($list as $key=>$item) {
			$list[$key]['usage'] = $this->getUsage($item['id']);
		}
		
		return $list;
	}
	
	
	/**
	 * get advance list
	 */
	
	public function getAdvanceList($filter = array()) {
	
		$add_to_where = '';
		
		//created between filter
		if ($filter['created_from'] != false && $filter['created_to'] != false) {
			if  (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $filter['created_from']) || !preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $filter['created_to'])) {
				msg("Invalid format for created between. Must be YYYY-MM-DD", "error");
				return false;
			}
			$add_to_where .=" AND invoice.created BETWEEN '{$filter['created_from']}' AND '{$filter['created_to']}'";
		}
	
	    $sql = "
		    SELECT promotion.id, promotion.title, promotion.code_pattern, count(invoice.id) as count, sum(invoice.goods_net) as sum_goods_net, sum(basket.discount_net) as sum_discount_net
		    FROM ecommerce_promotion promotion
		    LEFT OUTER JOIN ecommerce_promotion_code code ON (code.promotion_id = promotion.id) 
			LEFT OUTER JOIN ecommerce_invoice invoice ON (invoice.order_id = code.order_id)
			LEFT OUTER JOIN ecommerce_order eorder ON (eorder.id = invoice.order_id)
			LEFT OUTER JOIN ecommerce_basket basket ON (basket.id = eorder.basket_id)
			WHERE invoice.status = 1
			$add_to_where
			GROUP BY promotion.id, promotion.title, promotion.code_pattern
			";
		
		if ($records = $this->executeSql($sql)) {
			return $records;
		} else {
			return false;
		}
	}
	
	
	/**
	 * detail
	 */
		
	public function getDetail($id) {
		
		$detail = $this->detail($id);
		$detail['other_data'] = unserialize($detail['other_data']);
		
		return $detail;
	}
	
	/**
	 * add
	 */
	
	public function addPromotion($data) {
	
		if ($this->checkValidPattern($data['code_pattern'])) {
			$data['publish'] = 0;
			$data['created'] = date('c');
			$data['modified'] = date('c');
			$data['customer_account_type'] = 0;
			if (!is_numeric($data['discount_percentage_value'])) $data['discount_percentage_value'] = 0;
			if (!is_numeric($data['discount_fixed_value'])) $data['discount_fixed_value'] = 0;
			$data['discount_free_delivery'] = 0;
			$data['uses_per_coupon'] = 0;
			$data['uses_per_customer'] = 0;
			$data['limit_delivery_country_id'] = 0;
			$data['limit_delivery_carrier_id'] = 0;
			if ($id = $this->insert($data)) return $id;
			else return false;
		} else {
			msg('This pattern is in conflict with other promotion pattern', 'error');
			return false;
		}
	}
	
	/**
	 * update
	 */
	 
	public function updatePromotion($data) {
	
		if (!$this->checkValidPattern($data['code_pattern'], $data['id'])) {
			msg('This pattern is in conflict with other promotion pattern', 'error');
			return false;
		}
		
		$data['other_data'] = serialize($data['other_data']);
		
		if ($this->update($data)) return true;
		else return false;
	}
	
	/**
	 * check pattern
	 */
	
	public function checkValidPattern($pattern, $promotion_id = 0) {
	
		$records = $this->listing();
		foreach ($records as $record) {
			//msg("{$record['code_pattern']} $code");
			if ($promotion_id != $record['id']) {
				if (preg_match("/{$record['code_pattern']}/i", $pattern)) return false;
				if (preg_match("/$pattern/i", $record['code_pattern'])) return false;
			}
		}
		
		return true;
	}
	
	/**
	 * check code match
	 */

	public function checkCodeMatch($code, $only_public = 1) {
	
		$records = $this->listing();
		
		foreach ($records as $record) {
			//msg("{$record['code_pattern']} $code");
			if ($record['publish'] == 1 || $only_public == 0) {
				
				//allow forward slashes to be used as a string
				$item_code_pattern = preg_replace("/\//", '\/', $record['code_pattern']);
				
				if (preg_match("/{$item_code_pattern}/i", $code)) {
					
					$compaign_data = $record;
					
					$compaign_data['other_data'] = unserialize($compaign_data['other_data']);
					
					return $compaign_data;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * check if existing code can be used
	 */
	 
	public function checkCodeBeforeApply($code, $customer_id) {
		
		if (!is_numeric($customer_id)) {
			msg("ecommerce_promotion.checkCodeBeforeApply(): customer_id is not numeric", 'error');
			return false;
		}
		
		if ($compaign_data = $this->checkCodeMatch($code)) {
			
			/**
			 *  uses_per_coupon
			 */
			 
			if ($compaign_data['uses_per_coupon'] > 0) {
				if (($this->getCountUsageOfSingleCode($code) + 1) > $compaign_data['uses_per_coupon']) {
					msg("Code $code usage exceed number of allowed applications", 'error');
					return false;
				}
			}
			
			/**
			 * uses_per_customer
			 */
			 
			if ($compaign_data['uses_per_customer'] > 0) {
				
				if (($this->getCountUsageOfSingleCode($code, $customer_id) + 1) > $compaign_data['uses_per_customer']) {
					msg("Code $code usage exceed number of allowed applications per one customer (id=$customer_id)", 'error');
					return false;
				}
			}
		
			return $compaign_data;
			
		} else {
		
			return false;
		}
		
	}
	
	/**
	 * get usage
	 */
	
	public function getUsage($id) {
	
	    $sql = "
		    SELECT count(invoice.id) as count, sum(invoice.goods_net) as sum_goods_net, sum(basket.discount_net) as sum_discount_net
		    FROM ecommerce_promotion_code code 
			LEFT OUTER JOIN ecommerce_invoice invoice ON (invoice.order_id = code.order_id)
			LEFT OUTER JOIN ecommerce_order eorder ON (eorder.id = invoice.order_id)
			LEFT OUTER JOIN ecommerce_basket basket ON (basket.id = eorder.basket_id)
			WHERE code.promotion_id = $id AND invoice.status = 1";
		
		if ($records = $this->executeSql($sql)) {
			return $records[0];
		} else {
			return false;
		}
	}
	
	/**
	 * count usage
	 */
	
	public function getCountUsageOfSingleCode($code, $customer_id = false) {
	
		require_once('models/ecommerce/ecommerce_promotion_code.php');
		$Promotion_code = new ecommerce_promotion_code();
		$Promotion_code->setCacheable(false);
		
		$usage_list = $Promotion_code->getUsageOfSingleCode($code, $customer_id);

		if (is_array($usage_list)) {
			return count($usage_list);
		} else {
			return false;
		}
	}
	
	/**
	 * get code for an order
	 */
	
	public function getPromotionCodeForOrder($order_id) {
	
		if (!is_numeric($order_id)) return false;
		
		require_once('models/ecommerce/ecommerce_promotion_code.php');
		$Promotion_code = new ecommerce_promotion_code();
		
		return $Promotion_code->getPromotionCodeForOrder($order_id);
		
	}

	/**
	 * Apply promotion code
	 */
	
	function applyPromotionCodeToBasket($code, $basket_data) {

		if (!is_array($basket_data)) {
			msg("ecommerce_promotion.applyPromotionCodeToBasket: missing basket data", 'error');
			return false;
		}

		$customer_id = $basket_data['customer_id'];

		if ($compaign_data = $this->checkCodeBeforeApply($code, $customer_id)) {			
			
			//msg("Your code gives you {$compaign_data['discount_percentage_value']} discount");
			
			$discount_value = 0;
			
			if ($compaign_data['discount_percentage_value'] > 0) {
				$discount_value = $basket_data['content']['total_goods_net_before_discount'] * $compaign_data['discount_percentage_value']/100;
			}
			
			if ($compaign_data['discount_fixed_value'] > 0) {
				$discount_value = $compaign_data['discount_fixed_value'];
			}
			
			return $discount_value;
			
		} else {
			return false;
		}
	}
}