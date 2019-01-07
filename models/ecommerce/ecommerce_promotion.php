<?php
/**
 * class ecommerce_promotion (consider renaming to ecommerce_voucher)
 *
 * Copyright (c) 2009-2013 Onxshop Ltd (https://onxshop.com)
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

    /* promotion type */
    public $type;

    /* limit_cumulative_discount */
    public $limit_cumulative_discount;

    /* free_promo_products */
    public $free_promo_products;

    public $_metaData = array(
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
        'limit_to_order_amount'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'type'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'limit_cumulative_discount'=>array('label' => '', 'validation'=>'decimal', 'required'=>false),
        'free_promo_products'=>array('label' => '', 'validation'=>'string', 'required'=>false)
        );
    
    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "CREATE TABLE ecommerce_promotion (
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
            uses_per_coupon integer NOT NULL DEFAULT 0,
            uses_per_customer smallint NOT NULL DEFAULT 0,
            limit_list_products text,
            other_data text,
            limit_delivery_country_id smallint NOT NULL DEFAULT 0,
            limit_delivery_carrier_id smallint NOT NULL DEFAULT 0,
            generated_by_order_id integer REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT,
            generated_by_customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
            limit_by_customer_id integer DEFAULT 0 REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
            limit_to_first_order smallint NOT NULL DEFAULT 0,
            limit_to_order_amount numeric(12,5) DEFAULT 0,
            type integer NOT NULL DEFAULT 1 REFERENCES ecommerce_promotion_type(id) ON UPDATE CASCADE ON DELETE CASCADE,
            limit_cumulative_discount numeric(12,5) DEFAULT 0,
            free_promo_products text
            );
        ";
        
        return $sql;
    }

    /**
     * init configuration
     */
     
    static function initConfiguration() {
    
        if (array_key_exists('ecommerce_promotion', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_promotion'];
        else $conf = array();

        // Total number of referrals every customer can have.
        if (!is_numeric($conf['available_referrals_per_person'])) $conf['available_referrals_per_person'] = 10;

        // An amount both customers (inviting and invited) will receive
        if (!is_numeric($conf['discount_value'])) $conf['discount_value'] = 5;

        // Mimimum order amount to placed
        if (!is_numeric($conf['minimum_order_amount'])) $conf['minimum_order_amount'] = 30;

        // Welcome page id
        if (!is_numeric($conf['referral_page_id'])) $conf['referral_page_id'] = 5727;

        return $conf;
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

            if (is_numeric($filter['type'])) $where .= " AND promotion.type = {$filter['type']}";

            // text search
            if (strlen($filter['text_search']) > 0) {
                $s = pg_escape_string($filter['text_search']);
                $where .= " AND (promotion.title ILIKE '%$s%' OR promotion.code_pattern ILIKE '%$s%')";
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
                sum(invoice.goods_net) as sum_goods_net, sum(basket.face_value_voucher) as sum_face_value_voucher, 
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

            foreach ($records as &$record) {

                $sql = "SELECT sum(basket_content.discount) as sum_discount
                    FROM ecommerce_promotion_code code 
                    LEFT JOIN ecommerce_invoice invoice ON (invoice.order_id = code.order_id)
                    LEFT JOIN ecommerce_order eorder ON (eorder.id = invoice.order_id)
                    LEFT JOIN ecommerce_basket basket ON (basket.id = eorder.basket_id)
                    LEFT JOIN ecommerce_basket_content basket_content ON (basket.id = basket_content.basket_id)
                    WHERE code.promotion_id = {$record['id']} AND invoice.status = 1
                    $add_to_where";

                if ($records2 = $this->executeSql($sql)) {
                    $record['sum_discount'] = $records2[0]['sum_discount'];
                }

            }

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
        $detail['free_promo_products'] = unserialize($detail['free_promo_products']);

        require_once('models/ecommerce/ecommerce_promotion_type.php');
        $Type = new ecommerce_promotion_type();
        $type = $Type->detail($detail['type']);
        $detail['type_title'] = $type['title'];
        
        return $detail;
    }
    
    /**
     * add
     */
    
    public function addPromotion($data) {
        
        if (!$this->codeExists($data['code_pattern'])) {
        
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
        
            msg('This code is in conflict with other promotion code', 'error');
            return false;
        
        }
    }
    
    /**
     * update
     */
     
    public function updatePromotion($data) {
    
        if ($this->codeExists($data['code_pattern'], $data['id'])) {
            msg('This code is in conflict with other promotion code', 'error');
            return false;
        }
        
        $data['other_data'] = serialize($data['other_data']);
        $data['free_promo_products'] = serialize($data['free_promo_products']);
        
        if ($this->update($data)) return true;
        else return false;
    }
    
    /**
     * check if code exists
     */
    
    public function codeExists($code, $promotion_id = 0) {
    
        $code = pg_escape_string($code);
        $where = "code_pattern = '$code'";
        if (is_numeric($promotion_id) && $promotion_id > 0) $where .= " AND id <> $promotion_id";

        $records = $this->listing($where);

        return (count($records) > 0);

    }
    
    /**
     * check code match
     */

    public function checkCodeMatch($code, $only_public = 1) {
    
        $records = $this->listing();
        
        foreach ($records as $record) {

            if ($record['publish'] == 1 || $only_public == 0) {
                
                if (strtolower($code) == strtolower($record['code_pattern'])) {
                    
                    $promotion_data = $record;
                    $promotion_data['other_data'] = unserialize($promotion_data['other_data']);
                    $promotion_data['free_promo_products'] = unserialize($promotion_data['free_promo_products']);
                    
                    return $promotion_data;
                }
            }
        }
        
        return false;
    }

    /**
     * check code match
     */

    public function checkCodeMatchPartially($code, $only_public = 1) {
    
        $records = $this->listing();
        
        foreach ($records as $record) {

            if ($record['publish'] == 1 || $only_public == 0) {
                
                if (strtolower($code) == strtolower(substr($record['code_pattern'], 0, strlen($code)))) {
                    
                    $promotion_data = $record;
                    $promotion_data['other_data'] = unserialize($promotion_data['other_data']);
                    $promotion_data['free_promo_products'] = unserialize($promotion_data['free_promo_products']);
                    
                    return $promotion_data;
                }
            }
        }
        
        return false;
    }

    /**
     * check if free delivery is available for promotion detail, carrier and delivery country
     * @param  string  $code                Code to test
     * @param  int     $country_id          Destination country id
     * @param  int     $carrier_id          Choosed carrier
     * @return boolean
     */
    public function freeDeliveryAvailable($carrier_id, $country_id, $promotion_detail)
    {
        if ($promotion_detail['discount_free_delivery'] != 1) return false;

        // delivery country restriction
        if ($promotion_detail['limit_delivery_country_id'] > 0 && 
            $promotion_detail['limit_delivery_country_id'] != $country_id) return false;

        // delivery method restriction
        if ($promotion_detail['limit_delivery_carrier_id'] > 0 && 
            $promotion_detail['limit_delivery_carrier_id'] != $carrier_id) return false;

        return true;
    }
    
    /**
     * check if existing code can be used
     */
     
    public function checkCodeBeforeApply($code, $customer_id, $basket, $customer_email = '') {

        if ($promotion_data = $this->checkCodeMatch($code)) {

            // make sure to avoid rounding errors
            $order_value = round($basket['sub_total']['price'], 2);

            /**
             * check if customer_id is needed (some codes can be used in guest checkout mode)
             */
            if ($customer_id == 0 && ($promotion_data['uses_per_customer'] > 0 || $promotion_data['limit_cumulative_discount'] > 0 ||
                $promotion_data['generated_by_customer_id'] > 0 || $promotion_data['limit_by_customer_id'] > 0)) {

                if (!Zend_Registry::isRegistered('ecommerce_promotion:login_needed')) {

                    if ($_SESSION['client']['customer']['guest']) {
                        msg("We're sorry, but this voucher code cannot be used in conjunction with “Guest Checkout”. Please go to" .
                            " previous step and select “Save Details for Next Order” to create an account.", 'error');
                    } else {
                        msg("You have to login or register to use your voucher code.", 'error');
                    }

                    Zend_Registry::set('ecommerce_promotion:login_needed', true);
                }

                return false;
            }
        
            /**
             * first order
             */
            if ($promotion_data['limit_to_first_order'] > 0) {
                if ($this->getNumCustomersOrders($customer_id, $customer_email) > 0) {
                    if ($this->getNumCustomersPaidOrders($customer_id, $customer_email) == 0) {
                        if (!Zend_Registry::isRegistered('ecommerce_promotion:first_order_unpaid')) {
                            msg("Code \"$code\" can only be applied to your first order. If you cancelled " . 
                                "your previous order, please either return to it in “My Account” or contact " . 
                                "customer services if you wish to continue with your current order.", 
                                'error');
                            Zend_Registry::set('ecommerce_promotion:first_order_unpaid', true);
                        }
                    } else {
                        if (!Zend_Registry::isRegistered('ecommerce_promotion:first_order')) {
                            msg("We are sorry, this voucher is only valid on your first order", 'error');
                            Zend_Registry::set('ecommerce_promotion:first_order', true);
                        }
                    }
                    return false;
                }
            }

            /**
             *  uses_per_coupon
             */
             
            if ($promotion_data['uses_per_coupon'] > 0) {
                if (($this->getCountUsageOfSingleCode($code) + 1) > $promotion_data['uses_per_coupon']) {
                    if (substr($promotion_data['code_pattern'], 0, 4) != "REF-") { // referral codes validity is extended automatically
                        if (!Zend_Registry::isRegistered('ecommerce_promotion:total_usage_exceeded')) {
                            msg("Code \"$code\" usage exceed number of allowed applications", 'error');
                            Zend_Registry::set('ecommerce_promotion:total_usage_exceeded', true);
                        }
                        return false;
                    }
                }
            }

            /**
             * check uses_per_customer
             */

            if ($promotion_data['uses_per_customer'] > 0) {
                
                if (($this->getCountUsageOfSingleCode($code, $customer_id) + 1) > $promotion_data['uses_per_customer']) {
                    if (!Zend_Registry::isRegistered('ecommerce_promotion:per_user_usage_exceeded')) {
                        msg("Code \"$code\" usage exceed number of allowed applications per one customer", 'error');
                        Zend_Registry::set('ecommerce_promotion:per_user_usage_exceeded', true);
                    }
                    return false;
                }
            }

            /**
             * check limit_cumulative_discount
             */

            if ($promotion_data['limit_cumulative_discount'] > 0) {

                require_once('models/ecommerce/ecommerce_basket.php');
                $Basket = new ecommerce_basket();
                $Basket->calculateBasketDiscount($basket, $code, false);
                $usage = $this->getUsage($promotion_data['id'], $customer_id);
                if ($usage && ($usage['sum_discount'] + $basket['discount']) > $promotion_data['limit_cumulative_discount']) {
                    $limit = money_format("%n", $promotion_data['limit_cumulative_discount']);
                    $provided = money_format("%n", $usage['sum_discount']);
                    if (!Zend_Registry::isRegistered('ecommerce_promotion:limit_cumulative_discount_exceeded')) {
                        $msg = "Code \"$code\" is limited to maximum discount value of $limit. " .
                            "You’ve already used of {$provided}";
                        if ($promotion_data['discount_percentage_value'] > 0) {
                            $max_order = ($promotion_data['limit_cumulative_discount'] - $usage['sum_discount']) / ($promotion_data['discount_percentage_value'] / 100);
                            if ($max_order > 0) {
                                $max_order = money_format("%n", $max_order);
                                $msg .= ", so your current order would exceed your allotted discount value. " .
                                        "Please try again with an order of no greater than $max_order";
                                }
                        }
                        $msg .= ".";
                        msg($msg, 'error');
                        Zend_Registry::set('ecommerce_promotion:limit_cumulative_discount_exceeded', true);
                    }
                    return false;
                }
            }

            /**
             * not using self-generated code
             */
            if ($promotion_data['generated_by_customer_id'] > 0 && $promotion_data['generated_by_customer_id'] == $customer_id) {
                if (!Zend_Registry::isRegistered('ecommerce_promotion:own_code')) {
                    msg("You are not allowed to redeem your own code!");
                    Zend_Registry::set('ecommerce_promotion:own_code', true);
                }
                return false;
            }

            /**
             * code limited to customer_id
             */
            if ($promotion_data['limit_by_customer_id'] > 0 && $promotion_data['limit_by_customer_id'] != $customer_id) {
                if (!Zend_Registry::isRegistered('ecommerce_promotion:limit_by_customer_id')) {
                    msg("You are not allowed to redeem the code!");
                    Zend_Registry::set('ecommerce_promotion:limit_by_customer_id', true);
                }
                return false;
            }

            /**
             * minimum order amount
             */
            if ($promotion_data['limit_to_order_amount'] > 0) {
                if ($order_value < $promotion_data['limit_to_order_amount']) {
                    if (!Zend_Registry::isRegistered('ecommerce_promotion:order_amount')) {
                        $amount = money_format("%n", $promotion_data['limit_to_order_amount']);
                        $sub_total = money_format("%n", $order_value);
                        msg("The voucher code \"$code\" is restricted to orders in amount of $amount. You have only $sub_total in you basket.", 'error');
                        Zend_Registry::set('ecommerce_promotion:order_amount', true);
                    }
                    return false;
                }
            }

            /**
             * do not allow to buy gift voucher
             */
            if (substr($promotion_data['code_pattern'], 0, 4) == "REF-" || substr($promotion_data['code_pattern'], 0, 4) == "REW-") {
                $gift_voucher_product_id = (int) $this->getGiftVoucherProductId();
                if ($gift_voucher_product_id > 0 && count($basket['items']) > 0) {
                    foreach ($basket['items'] as $item) {
                        if ($item['product']['id'] == $gift_voucher_product_id) {
                            if (!Zend_Registry::isRegistered('ecommerce_promotion:gift_in_basket')) {
                                msg("Sorry, voucher codes cannot be used to buy Gift Voucher Codes.");
                                Zend_Registry::set('ecommerce_promotion:gift_in_basket', true);
                            }
                            return false;
                        }
                    }
                }
            }

            /**
             * check if limited products are in basket
             */
            $limited_ids = explode(",", $promotion_data['limit_list_products']);
            if (strlen($promotion_data['limit_list_products']) > 0 && is_array($limited_ids)) {
                $prod = 0;
                if (count($basket['items']) > 0) {
                    foreach ($basket['items'] as $item) {
                        if (in_array($item['product']['id'], $limited_ids)) $prod++;
                    }
                }
                if ($prod == 0) {
                    if (!Zend_Registry::isRegistered('ecommerce_promotion:limit_to_products')) {
                        msg("Sorry, the voucher code \"$code\" is limited to certain products, which you don't have in your basket.");
                        Zend_Registry::set('ecommerce_promotion:limit_to_products', true);
                    }
                    return false;
                }
            }

            /**
             * check if free promo item can be addeded to order
             */
            $promotion_data['free_promo_product'] = $this->checkForFreePromoItem($promotion_data, $order_value);

            require_once('models/ecommerce/ecommerce_promotion_type.php');
            $Type = new ecommerce_promotion_type();
            $promotion_data['type'] = $Type->detail($promotion_data['type']);

            return $promotion_data;
            
        } else {
        
            return false;
        }
        
    }

    /**
     * checkForFreePromoItem
     */
    
    public function checkForFreePromoItem($promotion_data, $order_value) {

        if (is_array($promotion_data)) {

            $product_variety_id = $promotion_data['free_promo_products'][9999];

            if (is_numeric($promotion_data['free_promo_products'][9999])) {
                require_once('models/ecommerce/ecommerce_product.php');
                $Product = new ecommerce_product();
                $variety = $Product->getProductVarietyDetail($product_variety_id);
                if ($variety) {
                    $variety['product'] = $Product->getDetail($variety['product_id']);
                    return $variety;
                }
            }
        }

        return false;
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
     * Get number of all orders for given customer id and email address
     * @param  int $customer_id    Customer id
     * @param  int $customer_email Customer email
     * @return int                 Number of orders
     */
    public function getNumCustomersOrders($customer_id, $customer_email)
    {
        $customer_id = (int) $customer_id;
        $email = pg_escape_string($customer_email);

        if ($customer_id == 0 && strlen($customer_email) == 0) return false;

        $sql = "SELECT COUNT(*) AS count FROM ecommerce_order AS o
            INNER JOIN ecommerce_basket AS b ON (b.id = o.basket_id)";

        if (strlen($customer_email) > 0) $sql .= " INNER JOIN client_customer AS c ON (c.email = '$email' AND c.id = b.customer_id)";

        $sql .= " WHERE o.status <> 4";

        if ($customer_id > 0) $sql .= " AND b.customer_id = $customer_id";

        $this->setCacheable(false);
        if ($records = $this->executeSql($sql)) return (int) $records[0]['count'];
        else return false;

    }


    /**
     * Get number of paid orders for given customer
     * @param  int $customer_id Customer id
     * @param  int $customer_email Customer email
     * @return int              Number of orders
     */
    public function getNumCustomersPaidOrders($customer_id, $customer_email)
    {
        $customer_id = (int) $customer_id;
        $email = pg_escape_string($customer_email);

        if ($customer_id == 0 && strlen($customer_email) == 0) return false;

        $sql = "SELECT COUNT(*) AS count FROM ecommerce_order AS o
            INNER JOIN ecommerce_basket AS b ON (b.id = o.basket_id)
            INNER JOIN ecommerce_invoice AS i ON i.order_id = o.id AND i.status <> 4";

        if (strlen($customer_email) > 0) $sql .= " INNER JOIN client_customer AS c ON (c.email = '$email' AND c.id = b.customer_id)";

        if ($customer_id > 0) $sql .= "WHERE b.customer_id = $customer_id";

        $this->setCacheable(false);
        if ($records = $this->executeSql($sql)) return (int) $records[0]['count'];
        else return false;

    }

    /**
     * get usage
     */
    
    public function getUsage($id, $customer_id = false) {
    
        if (is_numeric($customer_id)) $customer_sql = "AND customer_id = $customer_id";
        else $customer_sql = '';

        $sql = "SELECT count(invoice.id) as count, sum(invoice.goods_net) as sum_goods_net, 
                sum(basket.face_value_voucher) as sum_face_value_voucher
            FROM ecommerce_promotion_code code 
            INNER JOIN ecommerce_invoice invoice ON invoice.order_id = code.order_id AND invoice.status = 1
            LEFT JOIN ecommerce_order eorder ON eorder.id = invoice.order_id
            LEFT JOIN ecommerce_basket basket ON basket.id = eorder.basket_id $customer_sql
            WHERE code.promotion_id = $id";

        if ($records = $this->executeSql($sql)) {

            $result = $records[0];

            $sql = "SELECT sum(basket_content.discount) as sum_discount
                FROM ecommerce_promotion_code code 
                INNER JOIN ecommerce_invoice invoice ON invoice.order_id = code.order_id AND invoice.status = 1
                LEFT JOIN ecommerce_order eorder ON eorder.id = invoice.order_id
                LEFT JOIN ecommerce_basket basket ON basket.id = eorder.basket_id $customer_sql
                LEFT JOIN ecommerce_basket_content basket_content ON basket.id = basket_content.basket_id
                WHERE code.promotion_id = $id";

            if ($records = $this->executeSql($sql)) {
                $result['sum_discount'] = $records[0]['sum_discount'];
            }

            return $result;
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
            $result = 0;
            foreach ($usage_list as $usage) {
                if ($usage['status'] <> 4) $result++; // do not count cancelled orders
            }
            return $result;
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

            // check if a user is not already rewarded
            $this->setCacheable(false);
            $rewarded_codes = $this->listing("generated_by_order_id = $order_id");

            if (count($rewarded_codes) == 0) {

                // automatically extend to 20 invites
                if ($promotion['uses_per_coupon'] > 0 && $usage > $promotion['uses_per_coupon'] && $usage < 20) 
                {
                    $promotion['uses_per_coupon'] += 10;
                    $this->updatePromotion($promotion);
                }

                $data = array(
                    'title' => "Referral voucher code",
                    'description' => '',
                    'publish' => 1,
                    'type' => 3, // Referral Reward Coupon
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

                $invited_customer_id = $order_detail['basket']['customer_id'];
                $rewarded_customer_id = $promotion['generated_by_customer_id'];
                $code = $data['code_pattern'];

                $this->sendRewardEmail($invited_customer_id, $rewarded_customer_id, $code, $usage);

                // send warning email when 15 invites reached
                if ($usage == 15) $this->sendWarningEmail($rewarded_customer_id);

                return true;

            }
        }

        return false;
    }

    /**
     * Send warning email when a customer reaches 15 invites
     */
    public function sendWarningEmail($rewarded_customer_id)
    {
        require_once('models/common/common_email.php');
        require_once('models/client/client_customer.php');
        $EmailForm = new common_email();
        $Customer = new client_customer();
        $Customer->setCacheable(false);

        $rewarded_customer = $Customer->getDetail($rewarded_customer_id);

        $GLOBALS['common_email']['rewarded_customer'] = $rewarded_customer;

        $to_email = false; // admin
        $to_name = false;

        $EmailForm->sendEmail('referral_warning', 'n/a', $to_email, $to_name);
    }

    /**
     * Send email after succesfull reward code allocation
     */
    public function sendRewardEmail($invited_customer_id, $rewarded_customer_id, $code, $usage)
    {
        require_once('models/common/common_email.php');
        require_once('models/client/client_customer.php');
        $EmailForm = new common_email();
        $Customer = new client_customer();
        $Customer->setCacheable(false);

        $rewarded_customer = $Customer->getDetail($rewarded_customer_id);
        $invited_customer = $Customer->getDetail($invited_customer_id);

        $GLOBALS['common_email']['invited_customer'] = $invited_customer;
        $GLOBALS['common_email']['rewarded_customer'] = $rewarded_customer;
        $GLOBALS['common_email']['code'] = $code;
        $GLOBALS['common_email']['total_invited'] = $usage;

        $conf = ecommerce_promotion::initConfiguration();
        $GLOBALS['common_email']['minimum_order_amount'] = $conf['minimum_order_amount'];
        $GLOBALS['common_email']['discount_value'] = $conf['discount_value'];

        $to_email = $rewarded_customer['email'];
        $to_name = $rewarded_customer['first_name'] . " " . $rewarded_customer['last_name'];

        $EmailForm->sendEmail('referral_reward', 'n/a', $to_email, $to_name);
    }

    /**
     * Generate random code
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
