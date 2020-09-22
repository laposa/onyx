<?php
/**
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * class ecommerce_price
 * Regular Price, Sale Price, Fully Option Based, Recurring Only, Volume Based and
 * We can overwrite some price for some customer - basket_content
 * customer_id_price
 *
 */
 
class ecommerce_price extends Onyx_Model {

    /**
     * @access private
     */
    var $id;
    /**
     * REFERENCES product_variety(id) ON UPDATE CASCADE ON DELETE CASCADE
     * @access private
     */
    var $product_variety_id;
    /**
     * REFERENCES currency(code) ON UPDATE CASCADE ON DELETE CASCADE
     * @access private
     */
    var $currency_code;
    /**
     * @access private
     */
    var $value;
    /**
     * normal, discount, trade, trade_discount, cost
     * @access private
     */
    var $type;
    /**
     * @access private
     */
    var $date;
    
    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'product_variety_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'currency_code'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'value'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
        'type'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'date'=>array('label' => '', 'validation'=>'datetime', 'required'=>true)
    );

    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE ecommerce_price (
    id serial NOT NULL PRIMARY KEY,
    product_variety_id integer REFERENCES ecommerce_product_variety ON UPDATE CASCADE ON DELETE CASCADE,
    currency_code character(3),
    value numeric(12,5),
    \"type\" character varying(255),
    date timestamp(0) without time zone
);
        ";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration() {
    
        if (array_key_exists('ecommerce_price', $GLOBALS['onyx_conf'])) $conf = $GLOBALS['onyx_conf']['ecommerce_price'];
        else $conf = array();

        $conf['default_currency'] = $GLOBALS['onyx_conf']['global']['default_currency'];

        if (empty($conf['type'])) $conf['type'] = array('common');      
        else $conf['type'] = explode(",", $conf['type']);
        
        if (!array_key_exists('allow_multiplicator', $conf)) $conf['allow_multiplicator'] = 0;//disabled multiplicator functionality by default
        if (!array_key_exists('multiplicator_growth', $conf)) $conf['multiplicator_growth'] = 'linear';//linear or exponential_over_1
        if ($conf['multiplicator_exponent'] > 0) $conf['multiplicator_exponent'] = (float) $conf['multiplicator_exponent'];
        else $conf['multiplicator_exponent'] = 2; // exponential growth is 2 by default
        
        return $conf;
    }
    
    /**
     * getPrice
     * @return 
     * @access public
     */
     
    function getPrice($variety_id, $price_id = 0, $currency_code = GLOBAL_DEFAULT_CURRENCY) {
    
        if ($price_id > 0) {
            $price = $this->getPriceDetail($price_id, $currency_code);
        } else {
            $price = $this->getLastPriceForVariety($variety_id, $currency_code);
        }
        
        return $price;
    } 
    
    /**
     * get price detail
     */
    
    function getPriceDetail($price_id, $currency_code = GLOBAL_DEFAULT_CURRENCY) {
    
        $price_detail = $this->detail($price_id);

        if ($currency_code !=  $this->conf['default_currency']) {
            $price_detail['value'] = $this->convertCurrency($price_detail['value'], $this->conf['default_currency'], $currency_code);
            $price_detail['currency_code'] = $currency_code;
        }
        
        $price_detail = $this->format($price_detail);
        return $price_detail;
    }
    
    /**
     * find usage, how many times was added to basket
     */
     
    function getAddedToBasketCount($id) {
    
        if (!is_numeric($id)) return false;
        
        $sql = "SELECT count(id) FROM ecommerce_basket_content WHERE price_id = $id";
        
        if ($records = $this->executeSql($sql)) {
            return $records[0]['count'];
        } else {
            return false;
        }
        
        
    }
    
    /**
     * get price list
     */
    
    function getPriceList($variety_id, $price_type = 'common', $currency_code = GLOBAL_DEFAULT_CURRENCY) {
    
        $list = $this->listing("product_variety_id = $variety_id");
        
        return $list;
    }
    
    /**
     * insert price
     * 
     */
     
    function priceInsert($data) {

        if (!is_numeric($data['value'])) {
            msg('ecommerce_price.priceInsert: Net Value is not numeric', 'error');
            return false;
        }
        
        $data['date'] = date('c');
        
        if ($id = $this->insert($data)) return $id;
        else return false;
    }
    
    /**
     * update price (it's really creating a copy with a new value)
     * 
     */
     
    function priceUpdate($data) {
    
        if (!is_numeric($data['value'])) return false;

        //copy and insert new one
        $old = $this->detail($data['id']);
        $new['product_variety_id'] = $old['product_variety_id'];
        $new['currency_code'] = $old['currency_code'];
        $new['value'] = $data['value'];
        $new['type'] = $old['type'];
        $new['date'] = date('c');

        if ($id = $this->priceInsert($new)) {
            return $id;
        } else  {
            return false;
        }
    }
    
    /**
     * get last price 
     * 
     * 
     */
    
    function getLastPriceForVariety($variety_id, $currencty_code = GLOBAL_DEFAULT_CURRENCY, $type = 'common') {
    
        $price_list = $this->listing("product_variety_id = $variety_id AND currency_code = '$currencty_code' AND type = '$type'", 'date DESC', '0,1');
        if (count($price_list) == 0) {
            $price_list = $this->listing("product_variety_id = $variety_id AND currency_code = '" .  $this->conf['default_currency'] ."' AND type = '$type'", 'date DESC', '0,1');
            $price_default_currency = $price_list[0];
            $price = $price_default_currency;
            $price['currency_code'] = $currencty_code;
            $price['value'] = $this->convertCurrency($price_default_currency['value'],  $this->conf['default_currency'], $currencty_code);
        } else {
            $price = $price_list[0];
        }
        
        if (is_numeric($price['id'])) $price = $this->format($price);
        
        return $price;
    }
    
    /**
     * input array
     */
    
    function format($price) {
    
        if (!is_array($price) || !is_numeric($price['id'])) {
            msg('ecommerce_price.format: invalid input type, array expected', 'error');
            return false;
        }
        
        $vat = $this->getVatByPriceId($price['id']);
        $price['value_net'] = $price['value'];
        $price['value_gross'] = $price['value'] + $price['value'] * $vat/100;
        $price['value'] = $price['value_gross'];
            
        return $price;
    }
    
    
    /**
     *
     * @return 
     * @access public
     */
    
    function getCurrencyValue($value, $currency_code) {
    
        $prices = $this->listing("product_variety_id = {$this->product_variety_id} AND currency_code='$currency_code'");

        if (count($prices) > 0) {
            // we have explicit price in this currency
            $price = $prices[0];
        } else {
            
            $price = $this->convertCurrency($value,  $this->conf['default_currency'], $currency_code);
        }
        
        return $price;
    }
    
    /**
     * convert currency
     */
    
    static function convertCurrency($value, $from, $to) {
    
        require_once('models/international/international_currency_rate.php');
        $Currency = new international_currency_rate();
            
        $destination_value = $Currency->convert($value, $from, $to);

        return $destination_value;
    }
    
    /**
     * get currencies
     */
    
    function getCurrencies($currency_code = 'conf') {
    
        require_once('models/international/international_currency.php');
        $Currency = new international_currency();
        $international_currency_conf = international_currency::initConfiguration();
        
        if ($currency_code == 'conf') {

            $cs = $Currency->conf['allowed'];

            $count = count($cs);
            for ($i = 0; $i < $count; $i++) {
                $where .= "code = '{$cs[$i]}'";
                if (($i + 1) < $count) $where .= " OR ";
            }
        } else if ($currency_code != 'all') {
            $where = "code = '$currency_code'";
        } else {
            $where = '';
        }
        
        $curs = $Currency->listing($where);

        foreach ($curs as $cur) {
            if ($cur['symbol_left'] == '' && $cur['symbol_right'] == '') $cur['symbol_right'] = "&nbsp;" . $cur['code'];
            $result[$cur['code']] = $cur;
        }
        
        return $result;
    }
    
    /**
     * get types
     */
     
    function getTypes() {
    
    /* FIX: DISABLE FOR NOW - VERY HEAVY
        $sql = "SELECT DISTINCT id, type FROM ecommerce_price ORDER BY id ASC"; 
        $records = $this->executeSql($sql);
        foreach ($records as $record) {
            $types_in_db[] = $record['type'];
        }*/
        $types_in_db[] = 'common';
        $types_in_config = $this->conf['type'];
        $types = array_unique(array_merge($types_in_config, $types_in_db));
        
        return $types;
    }

    /**
     * get VAT rate by variety_id
     */
     
    function getVatByVarietyId($variety_id) {
    
        if (!is_numeric($variety_id)) {
            msg('ecommerce_price.getVatByVarietyId: variety_id is not numeric', 'error');
            return false;
        }
        
        $sql = "SELECT t.vat FROM ecommerce_product p, ecommerce_product_variety v, ecommerce_product_type t WHERE v.product_type_id = t.id AND v.product_id = p.id AND v.id = $variety_id";
        
        if ($records = $this->executeSql($sql)) {
            return $records[0]['vat'];
        } else {
            return false;
        }
    }
    
    /**
     * get VAT rate by price_id
     */
    
    function getVatByPriceId($price_id) {
    
        if (!is_numeric($price_id)) {
            msg('ecommerce_price.getVatByPriceId: price_id is not numeric', 'error');
            return false;
        }
        
        $sql = "SELECT t.vat FROM ecommerce_product p, ecommerce_product_variety v, ecommerce_price price, ecommerce_product_type t WHERE v.product_type_id = t.id AND v.product_id = p.id AND v.id = price.product_variety_id AND price.id = $price_id";
        
        if ($records = $this->executeSql($sql)) {
            return $records[0]['vat'];
        } else {
            return false;
        }
    }
    
    /**
     * getCustomPriceIdByMultiplicator
     */
     
    public function getCustomPriceIdByMultiplicator($product_variety_id, $multiplicator) {
        
        if ($this->conf['allow_multiplicator'] == 0) {
            msg("Price multiplicator is disabled", 'error');
            return false;
        }
        
        if (!is_numeric($product_variety_id)) return false;
        if (!is_numeric($multiplicator)) return false;

        // need to check if min_price is not set
        require_once 'models/ecommerce/ecommerce_product_variety.php';
        $Product_Varity = new ecommerce_product_variety();
        $product_variety = $Product_Varity->getDetail($product_variety_id);
        $min_price = (int) $product_variety['other_data']['min_price'];

        // linear growth by default
        $exponent = 1;

        if ($this->conf['multiplicator_growth'] == "exponential_over_1") {
            // need to check if exponent is not set
            if (is_numeric($product_variety['other_data']['exponent'])) {
                $exponent = $product_variety['other_data']['exponent'];
            } else {
                // otherwise set it as per global configuration
                if (is_numeric($this->conf['multiplicator_exponent'])) $exponent = $this->conf['multiplicator_exponent'];
            }
        }

        $type = "multiplicator_{$multiplicator}";
        if ($min_price > 0) $type .= "_min_$min_price";
        if ($exponent != 1) $type .= "_exponent_$exponent";
        $price_data = $this->getLastPriceForVariety($product_variety_id, GLOBAL_DEFAULT_CURRENCY, $type);
        $common_price_data = $this->getLastPriceForVariety($product_variety_id);
        
        if (is_numeric($price_data['id']) && bccomp($common_price_data['value'], $price_data['value'], 3) == 0) {
        
            $price_id = $price_data['id'];

        } else {

            $price_data = array();
            $price_data['product_variety_id'] = $product_variety_id;
            
            switch ($this->conf['multiplicator_growth']) {
                case 'exponential_over_1':
                    //exponential for multiplicator value greater than 1, under 1 is linear
                    if ($multiplicator > 1) $price_data['value'] = $common_price_data['value'] * pow($multiplicator, $exponent);
                    else $price_data['value'] = $common_price_data['value'] * $multiplicator;
                    break;
                case 'linear':
                default:
                    //linear growth
                    $price_data['value'] = $common_price_data['value'] * $multiplicator;
                    break;
            }

            if ($price_data['value'] < $min_price) $price_data['value'] = $min_price;
            
            $price_data['currency_code'] = GLOBAL_DEFAULT_CURRENCY;
            $price_data['type'] = $type;
            $price_id = $this->priceInsert($price_data);

        }
        
        msg("Created custom price ID $price_id", 'ok', 2);
        
        if (is_numeric($price_id)) return $price_id;
        else return false;
    }
    
    /**
     * temporary implementation for bo/component/single_record_update
     */
    
    function updateSingleAttribute($attribute, $update_value, $id) {
    
        switch ($attribute) {
            case 'value':
                $data['id'] = $id;
                $data['value'] = $update_value;
                return $this->priceUpdate($data);
            break;
        }
    }
    
}
