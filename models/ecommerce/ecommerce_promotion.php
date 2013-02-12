<?php
/**
 * class ecommerce_promotion (consider renaming to ecommerce_voucher)
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
	
	public $generated_by_order_id;
	
	/* voucher author */
	public $generated_by_customer_id;
	
	/* voucher limited to specific customer (reward for inviting) */
	public $limit_by_customer_id;
	
	/* voucher limited to first order */
	public $limit_to_first_order;
	
	/* voucher limited to minim order amount */
	public $limit_to_order_amount;

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
		'limit_delivery_carrier_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'generated_by_order_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'generated_by_customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'limit_by_customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'limit_to_first_order'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'limit_to_order_amount'=>array('label' => '', 'validation'=>'int', 'required'=>false)
		);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_promotion (
    id serial NOT NULL PRIMARY KEY,
    title varchar(255) ,
    description text ,
    publish smallint NOT NULL DEFAULT 1,
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
    customer_account_type smallint NOT NULL DEFAULT 0,
    code_pattern varchar(255) NOT NULL,
    discount_fixed_value decimal(12,5) NOT NULL DEFAULT 0,
    discount_percentage_value decimal(5,2) NOT NULL DEFAULT 0,
    discount_free_delivery smallint NOT NULL DEFAULT 0,
    uses_per_coupon integer NOT NULL DEFAULT 0 ,
    uses_per_customer smallint NOT NULL DEFAULT 0,
    limit_list_products text ,
    other_data text,
	limit_delivery_country_id smallint NOT NULL DEFAULT 0,
	limit_delivery_carrier_id smallint NOT NULL DEFAULT 0,
	generated_by_order_id integer REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT,
	generated_by_customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
	limit_by_customer_id integer DEFAULT 0 REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
	limit_to_first_order smallint NOT NULL DEFAULT 0,
	limit_to_order_amount numeric(12,5) DEFAULT 0
	
);
		";
		
		return $sql;
	}
	
	/**
	 * list
	 */
		
	public function getList($offset = 0, $limit = 20, $filter = array()) {

		// set filtering criteria	
		$where = $this->prepareWhereConditions($filter);

		// create sql query
	    $sql =
		    "SELECT promotion.*, customer.title_before AS customer_title_before,
		    	customer.first_name AS customer_first_name, customer.last_name AS customer_last_name,
		    	CASE WHEN customer.id > 0 THEN
		    		(SELECT count(*) FROM common_email WHERE email_from = customer.email AND template = 'referral_invite')
		    		ELSE 0
		    	END AS customer_invite_count
		    FROM ecommerce_promotion AS promotion
		    LEFT JOIN client_customer AS customer ON customer.id = promotion.generated_by_customer_id
		    WHERE $where
			ORDER BY id DESC
			LIMIT $limit OFFSET $offset
			";

		if (!$list = $this->executeSql($sql)) {
			return false;
		}

		foreach ($list as $key=>$item) {
			$list[$key]['usage'] = $this->getUsage($item['id']);
		}
		
		return $list;
	}


	/**
	 * filtered count
	 */
		
	public function getFilteredCount($filter) {

		// set filtering criteria	
		$where = $this->prepareWhereConditions($filter);

		// create sql query
	    $sql =
		    "SELECT count(promotion.*) AS count
		    FROM ecommerce_promotion AS promotion
		    WHERE $where
			";

		if (!$list = $this->executeSql($sql)) {
			return false;
		}

		return $list[0]['count'];
	}

	/**
	 * Prapare SQL where conditions
	 * 
	 * @param  Array  $filter Filtering parameters
	 * @return String Part of SQL query
	 */
	protected function prepareWhereConditions($filter) {

		$where = '1 = 1';

		if (count($filter) > 0) {

			// voucher type filter
			switch ($filter['type']) {
				case "REF-": 
					$where .= " AND promotion.code_pattern LIKE 'REF-%'";
					break;
				case "REW-": 
					$where .= " AND promotion.code_pattern LIKE 'REW-%'";
					break;
				case "GIFT-": 
					$where .= " AND promotion.code_pattern LIKE 'GIFT-%'";
					break;
				case "other": 
					$where .= " AND promotion.code_pattern NOT LIKE 'REF-%'";
					$where .= " AND promotion.code_pattern NOT LIKE 'REW-%'";
					$where .= "AND promotion.code_pattern NOT LIKE 'GIFT-%'";
					break;
			}

			// text search
			if (strlen($filter['text_search']) > 0) {
				$s = pg_escape_string($filter['text_search']);
				$where .= " AND (promotion.title LIKE '%$s%' OR promotion.code_pattern LIKE '%$s%')";
			}

			//created between filter
			if ($filter['created_from'] != false ) {
				if  (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $filter['created_from'])) {
					msg("Invalid format for created between. Must be YYYY-MM-DD", "error");
					return false;
				}
				$where .=" AND promotion.created >= '{$filter['created_from']}'";
			}
			if ($filter['created_to'] != false ) {
				if  (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $filter['created_to'])) {
					msg("Invalid format for created between. Must be YYYY-MM-DD", "error");
					return false;
				}
				$where .=" AND promotion.created <= '{$filter['created_to']}'";
			}

		}

		return $where;
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
	
	    $sql =
		    "SELECT promotion.id, promotion.title, promotion.code_pattern, count(invoice.id) as count, 
			    sum(invoice.goods_net) as sum_goods_net, sum(basket.discount_net) as sum_discount_net, 
			    customer.title_before AS customer_title_before,
		    	customer.first_name AS customer_first_name, customer.last_name AS customer_last_name
		    FROM ecommerce_promotion promotion
		    LEFT OUTER JOIN ecommerce_promotion_code code ON (code.promotion_id = promotion.id) 
			LEFT OUTER JOIN ecommerce_invoice invoice ON (invoice.order_id = code.order_id)
			LEFT OUTER JOIN ecommerce_order eorder ON (eorder.id = invoice.order_id)
			LEFT OUTER JOIN ecommerce_basket basket ON (basket.id = eorder.basket_id)
			LEFT OUTER JOIN client_customer AS customer ON customer.id = promotion.generated_by_customer_id
			WHERE invoice.status = 1
			$add_to_where
			GROUP BY promotion.id, promotion.title, promotion.code_pattern,
				customer.title_before, customer.first_name, customer.last_name
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
		
			if (!is_numeric($data['publish'])) $data['publish'] = 0;
			$data['created'] = date('c');
			$data['modified'] = date('c');
			if (!is_numeric($data['customer_account_type'])) $data['customer_account_type'] = 0;
			if (!is_numeric($data['discount_percentage_value'])) $data['discount_percentage_value'] = 0;
			if (!is_numeric($data['discount_fixed_value'])) $data['discount_fixed_value'] = 0;
			if (!is_numeric($data['discount_free_delivery'])) $data['discount_free_delivery'] = 0;
			if (!is_numeric($data['uses_per_coupon'])) $data['uses_per_coupon'] = 0;
			if (!is_numeric($data['uses_per_customer'])) $data['uses_per_customer'] = 0;
			if (!is_numeric($data['limit_delivery_country_id'])) $data['limit_delivery_country_id'] = 0;
			if (!is_numeric($data['limit_delivery_carrier_id'])) $data['limit_delivery_carrier_id'] = 0;
			
			if (is_array($data['other_data'])) $data['other_data'] = serialize($data['other_data']);
			
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
				$record_code_pattern_regex_safe = preg_replace("/\//", '\/', $record['code_pattern']);
				if (preg_match("/$record_code_pattern_regex_safe/i", $pattern)) return false;
				$pattern_regex_safe = preg_replace("/\//", '\/', $pattern);
				if (preg_match("/$pattern_regex_safe/i", $record['code_pattern'])) return false;
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
					
					$campaign_data = $record;
					
					$campaign_data['other_data'] = unserialize($campaign_data['other_data']);
					
					return $campaign_data;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * check if existing code can be used
	 */
	 
	public function checkCodeBeforeApply($code, $customer_id, $basket_data) {

		if (!is_numeric($customer_id)) {
			msg("ecommerce_promotion.checkCodeBeforeApply(): customer_id is not numeric", 'error');
			return false;
		}

		if ($campaign_data = $this->checkCodeMatch($code)) {
			
			/**
			 *  uses_per_coupon
			 */
			 
			if ($campaign_data['uses_per_coupon'] > 0) {
				if (($this->getCountUsageOfSingleCode($code) + 1) > $campaign_data['uses_per_coupon']) {
					if (substr($campaign_data['code_pattern'], 0, 4) != "REF-") { // referral codes validity is extended automatically
						msg("Code \"$code\" usage exceed number of allowed applications", 'error');
						return false;
					}
				}
			}

			/**
			 * check uses_per_customer
			 */

			if ($campaign_data['uses_per_customer'] > 0) {
				
				if (($this->getCountUsageOfSingleCode($code, $customer_id) + 1) > $campaign_data['uses_per_customer']) {
					msg("Code \"$code\" usage exceed number of allowed applications per one customer (id=$customer_id)", 'error');
					return false;
				}
			}

			/**
			 * not using self-generated code
			 */
			if ($campaign_data['generated_by_customer_id'] > 0 && $campaign_data['generated_by_customer_id'] == $customer_id) {
				msg("You are not allowed to redeem your own code!");
				return false;
			}

			/**
			 * first order
			 */
			if ($campaign_data['limit_to_first_order'] > 0) {
				if ($this->getNumCustomerOrders($customer_id) > 0) {
					msg("Code \"$code\" is restricted to your first order.", 'error');
					return false;
				}
			}

			/**
			 * minimum order amount
			 */
			if ($campaign_data['limit_to_order_amount'] > 0) {
				if ($basket_data['content']['total_sub'] < $campaign_data['limit_to_order_amount']) {
					$amount = money_format("%n", $campaign_data['limit_to_order_amount']);
					msg("The voucher code \"$code\" is restricted to orders in amount of $amount.", 'error');
					return false;
				}
			}

			/**
			 * do not allow to buy gift voucher
			 */
			if (substr($campaign_data['code_pattern'], 0, 4) == "REF-" || substr($campaign_data['code_pattern'], 0, 4) == "REW-") {
				$gift_voucher_product_id = (int) $this->getGiftVoucherProductId();
				if ($gift_voucher_product_id > 0 && count($basket_data['content']['items']) > 0) {
					foreach ($basket_data['content']['items'] as $item) {
						if ($item['product']['id'] == $gift_voucher_product_id) {
							msg("Sorry, voucher codes cannot be used to buy Gift Voucher Codes.");
							return false;
						}
					}
				}
			}

			/**
			 * check if limited products are in basket
			 */
			$limited_ids = explode(",", $campaign_data['limit_list_products']);
			if (strlen($campaign_data['limit_list_products']) > 0 && is_array($limited_ids)) {
				$prod = 0;
				if (count($basket_data['content']['items']) > 0) {
					foreach ($basket_data['content']['items'] as $item) {
						if (in_array($item['product']['id'], $limited_ids)) $prod++;
					}
				}
				if ($prod == 0) {
					msg("Sorry, the voucher code \"$code\" is limited to certain products, which you don't have in your basket.");
					return false;
				}
			}

			return $campaign_data;
			
		} else {
		
			return false;
		}
		
	}


	/**
	 * getGiftVoucherProductId
	 */
	 
	public function getGiftVoucherProductId() {
		
		/**
		 * get product conf
		 */
		 
		require_once('models/ecommerce/ecommerce_product.php');
		$ecommerce_product_conf = ecommerce_product::initConfiguration();
		
		/**
		 * check gift voucher product ID is set
		 */
		 
		if (!is_numeric($ecommerce_product_conf['gift_voucher_product_id']) || $ecommerce_product_conf['gift_voucher_product_id']  == 0) {
			
			return false;
		}
		
		return $ecommerce_product_conf['gift_voucher_product_id'];
	}


	/**
	 * Get number of orders for given customer
	 * @param  int $customer_id Customer id
	 * @return int              Number of orders
	 */
	public function getNumCustomerOrders($customer_id)
	{
		$customer_id = (int) $customer_id;

	    $sql = "SELECT COUNT(*) AS count FROM ecommerce_order AS o
			LEFT JOIN ecommerce_basket AS b ON (b.id = o.basket_id)
			WHERE b.customer_id = $customer_id";

		$this->setCacheable(false);
		if ($records = $this->executeSql($sql)) return (int) $records[0]['count'];
		else return false;

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
	
	function applyPromotionCodeToBasket($code, $basket_data, $exclude_vat = false) {

		if (!is_array($basket_data)) {
			msg("ecommerce_promotion.applyPromotionCodeToBasket: missing basket data", 'error');
			return false;
		}
		
		$customer_id = $basket_data['customer_id'];

		if ($campaign_data = $this->checkCodeBeforeApply($code, $customer_id, $basket_data)) {
			
			//msg("Your code gives you {$campaign_data['discount_percentage_value']} discount");
			
			$discount_value = 0;

			$value_for_discount = $basket_data['content']['total_goods_net_before_discount'];
			if (!$exclude_vat) $value_for_discount += $basket_data['content']['total_vat'];

			// if discount is limit to certain products, the value is only the part of the basket
			$limited_ids = explode(",", $campaign_data['limit_list_products']);
			if (strlen($campaign_data['limit_list_products']) > 0 && is_array($limited_ids)) {
				$value_for_discount = 0;
				if (count($basket_data['content']['items']) > 0) {
					foreach ($basket_data['content']['items'] as $item) {
						if (in_array($item['product']['id'], $limited_ids)) 
							$value_for_discount += (float) ($exclude_vat ? $item['total_net'] : $item['total_inc_vat']);
					}
				}
			}
			
			// percentage value discount
			if ($campaign_data['discount_percentage_value'] > 0) {
				$discount_value = $value_for_discount * $campaign_data['discount_percentage_value']/100;
			}
			
			// fixed value discount
			if ($campaign_data['discount_fixed_value'] > 0) {
				$discount_value = $campaign_data['discount_fixed_value'];
			}

			$discount_value = round($discount_value, 5);
			
			return $discount_value;
			
		} else {
			return false;
		}
	}

	/**
	 * Try to reward inviting user in referreal system
	 * 
	 * @param  int $order_id Order to check
	 * @return boolean If attempt was successfull
	 */
	public function rewardInvitingUser($order_id)
	{
		// get order detail
		require_once('models/ecommerce/ecommerce_order.php');
		$EcommerceOrder = new ecommerce_order();
		$EcommerceOrder->setCacheable(false);
		$order_detail = $EcommerceOrder->getFullDetail($order_id);

		// get promotion detail
		$code = pg_escape_string($this->getPromotionCodeForOrder($order_id));
		$promotions = $this->listing("code_pattern = '$code'");
		$promotion = $promotions[0];

		if (substr($promotion['code_pattern'], 0, 4) === "REF-") {

			$usage = $this->getCountUsageOfSingleCode($code) + 1;

			if ($promotion['uses_per_coupon'] > 0 && $usage > $promotion['uses_per_coupon']) 
			{
				$promotion['uses_per_coupon'] += 10;
				$this->updatePromotion($promotion);
				$emailTemplate = 'referral_reward_extend';
			}
			else
			{
				$emailTemplate = 'referral_reward';
			}


			// check if a user is not already rewarded
			$this->setCacheable(false);
			$rewarded_codes = $this->listing("generated_by_order_id = $order_id");

			if (count($rewarded_codes) == 0) {

				$data = array(
					'title' => "Referral voucher code",
					'description' => '',
					'publish' => 1,
					'code_pattern' => $this->generateRandomCode('REW-', 5, 5),
					'discount_fixed_value' => $promotion['discount_fixed_value'],
					'discount_percentage_value' => 0,
					'discount_free_delivery' => 0,
					'uses_per_coupon' => 1,
					'uses_per_customer' => 1,
					'limit_list_products' => '',
					'other_data' => NULL,
					'limit_delivery_country_id' => 0,
					'limit_delivery_carrier_id' => 0,
					'limit_by_customer_id' => $promotion['generated_by_customer_id'],
					'limit_to_first_order' => 0,
					'limit_to_order_amount' => $promotion['limit_to_order_amount'],
					'generated_by_order_id' => $order_id,
					'generated_by_customer_id' => $order_detail['basket']['customer_id']
				);

				$this->insert($data);

				// send email
				require_once('models/common/common_email.php');
				$EmailForm = new common_email();
				require_once('models/client/client_customer.php');
				$Customer = new client_customer();
				$Customer->setCacheable(false);
				$recipient = $Customer->getDetail($promotion['generated_by_customer_id']);

				$GLOBALS['common_email']['customer'] = $_SESSION['client']['customer'];
				$GLOBALS['common_email']['recipient'] = $recipient;
				$GLOBALS['common_email']['code'] = $data['code_pattern'];
				$GLOBALS['common_email']['total_invited'] = $usage;

				$EmailForm->sendEmail($emailTemplate, 'n/a', $recipient['email'], $recipient['first_name'] . 
					" " . $recipient['last_name']);

				// TODO: schedule email in two weeks

				return true;

			}
		}

		return false;
	}

	/**
	 * Generate random code
	 * @return [type] [description]
	 */
	public function generateRandomCode($prefix, $size1 = 6, $size2 = 0)
	{
		$result = $prefix;
		$alphabet = "ABCDEFGHJKLMNPQRSTVXYZ23456789";
		for ($i = 0; $i < $size1; $i++) {
			$result .= $alphabet[rand(0, strlen($alphabet) - 1)];
		}
		if ($size2 > 0) {
			$result .= "-";
			for ($i = 0; $i < $size2; $i++) {
				$result .= $alphabet[rand(0, strlen($alphabet) - 1)];
			}
		}

		// TODO: check if code is unique!

		return $result;
	}

}
