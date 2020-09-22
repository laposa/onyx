<?php
/**
 * class ecommerce_product_variety
 *
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_product_variety extends Onyx_Model {

    /**
     * @access private
     */
    var $id;
    /**
     * REFERENCES product(id) ON UPDATE CASCADE ON DELETE CASCADE
     * @access private
     */
    var $product_id;

    /**
     * @access private
     * product type (aka tax group)
     */
    var $product_type_id;

    /**
     * @access private
     * this is SKU
     */
    var $sku;
    /**
     * in gramme
     * @access private
     */
    var $weight;
    
    var $weight_gross;
    
    /**
     * @access private
     */
    var $stock;
    
    var $name;
    
    var $priority;
    
    var $description;
    
    var $other_data;
    
    var $width;
    
    var $height;
    
    var $depth;
    
    var $diameter;
    
    var $modified;
    
    var $publish;
    
    var $display_permission;

    var $ean13;

    var $upc;

    /**
     * 0 new (default)
     * 1 old
     * 2 refurbished
     */
    var $condition;
    
    /**
     * indicates if available for trade customers only
     */
     
    var $wholesale;
    
    var $reward_points;
    
    var $subtitle;

    var $limit_to_delivery_zones;

    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'product_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'product_type_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'sku'=>array('label' => 'SKU', 'validation'=>'string', 'required'=>true),
        'weight'=>array('label' => 'Product Weight Net', 'validation'=>'int', 'required'=>true),
        'weight_gross'=>array('label' => 'Product Weight Gross', 'validation'=>'int', 'required'=>true),
        'stock'=>array('label' => 'Stock', 'validation'=>'int', 'required'=>true),
        'name'=>array('label' => 'Name', 'validation'=>'string', 'required'=>true),
        'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'other_data'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'width'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'height'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'depth'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'diameter'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'modified'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'publish'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'display_permission'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'ean13'=>array('label' => 'EAN (European Article Number)', 'validation'=>'string', 'required'=>false),
        'upc'=>array('label' => 'UPC (Universal Product Code)', 'validation'=>'string', 'required'=>false),
        'condition'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'wholesale'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'reward_points'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'subtitle'=>array('label' => 'Name', 'validation'=>'string', 'required'=>false),
        'limit_to_delivery_zones'=>array('label' => 'Limit to delivery zones', 'validation'=>'string', 'required'=>false)
    );
    
    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "CREATE TABLE ecommerce_product_variety (
            id serial NOT NULL PRIMARY KEY,
            name character varying(255),
            product_id integer REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE CASCADE,
            product_type_id integer REFERENCES ecommerce_product_type ON UPDATE CASCADE ON DELETE CASCADE,
            sku character varying(255),
            weight integer,
            weight_gross integer,
            stock integer,
            priority integer DEFAULT 0 NOT NULL,
            description text,
            other_data text,
            width integer DEFAULT 0 NOT NULL,
            height integer DEFAULT 0 NOT NULL,
            depth integer DEFAULT 0 NOT NULL,
            diameter integer DEFAULT 0 NOT NULL,
            modified timestamp(0) without time zone DEFAULT now() NOT NULL,
            publish smallint DEFAULT 0 NOT NULL,
            display_permission integer NOT NULL DEFAULT 0,
            condition smallint NOT NULL DEFAULT 0,
            wholesale smallint,
            reward_points integer,
            subtitle varchar(255),
            limit_to_delivery_zones character varying(255)
        );

        ALTER TABLE ecommerce_product_variety ADD UNIQUE (\"sku\");
        ";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration() {
    
        if (array_key_exists('ecommerce_product_variety', $GLOBALS['onyx_conf'])) $conf = $GLOBALS['onyx_conf']['ecommerce_product_variety'];
        else $conf = array();
        
        /*
        units are in the database allways in grams, this is what to display/insert in the interface
        allowed g,kg,t
        */
        $conf['weight_units'] = 'g';
        $conf['dimension_units'] = 'mm';

        return $conf;
    }
    
    /**
     * get detail
     */
     
    public function getDetail($variety_id, $price_id = 0, $currency_code = GLOBAL_DEFAULT_CURRENCY) {
    
        return $this->getVarietyDetail($variety_id, $price_id, $currency_code);
        
    }
    
    /**
     * get variety detail
     */
     
    function getVarietyDetail($variety_id, $price_id = 0, $currency_code = GLOBAL_DEFAULT_CURRENCY) {
    
        require_once('models/ecommerce/ecommerce_price.php');
        $Price = new ecommerce_price();
        
        $variety = $this->detail($variety_id);
        
        $variety['weight'] = $this->convertWeight($variety['weight'], 'g', $this->conf['weight_units']);
        $variety['weight_gross'] = $this->convertWeight($variety['weight_gross'], 'g', $this->conf['weight_units']);
        
        if ($variety['other_data']) $variety['other_data'] = unserialize($variety['other_data']);
        
        $p = $Price->getPrice($variety_id, $price_id, $currency_code);

        $variety['price'] = $p;
    
        require_once('models/ecommerce/ecommerce_product_type.php');
        $ProductType = new ecommerce_product_type();
        $product_type_detail = $ProductType->detail($variety['product_type_id']);
        $variety['type'] = $product_type_detail;
        $variety['vat'] = $product_type_detail['vat'];
    
        return $variety;
    }
    
    /**
     * insert variety
     */
    
    function insertVariety($data) {
    
        $data['modified'] = date('c');
        $data['publish'] = 1;
        $data['priority'] = 0;
        $data['display_permission'] = 0;
        $data['condition'] = 0;
        
        if (!$data['width']) $data['width'] = 0;
        if (!$data['height']) $data['height'] = 0;
        if (!$data['depth']) $data['depth'] = 0;
        if (!$data['diameter']) $data['diameter'] = 0;
        
        if (is_array($data['other_data'])) $data['other_data'] = serialize($data['other_data']);
        
        $data['weight'] = $this->convertWeight($data['weight'], $this->conf['weight_units'], 'g');
        $data['weight_gross'] = $this->convertWeight($data['weight_gross'], $this->conf['weight_units'], 'g');

        if($id = $this->insert($data)) {
            return $id;
        } else {
            return false;
        }
    }
    
    /**
     * insert full product
     */
     
    public function insertFullVariety($data) {
    
        /**
         * check input values
         */
        
        if (!is_numeric($data['product_id'])) {
            msg('product_id is not numeric', 'error');
            return false;
        }

        if (!is_numeric($data['product_type_id'])) {
            msg('Product type id is not numeric', 'error');
            return false;
        }

        if (trim($data['name']) == "") {
            msg('Variety name is empty', 'error');
            return false;
        }
        
        if (trim($data['sku']) == "") {
            msg('Variety SKU is empty', 'error');
            return false;
        }
        
        if (!is_numeric($data['weight_gross'])) {
            msg('weight_gross is not numeric', 'error');
            return false;
        }
        
        if (!is_numeric($data['stock'])) {
            msg('Stock value is not numeric', 'error');
            return false;
        }
        
        if (trim($data['price']['currency_code']) == "") {
            msg('Currency code is empty', 'error');
            return false;
        }
        
        if (trim($data['price']['type']) == "") {
            msg('Price type', 'error');
            return false;
        }
        
        if (!is_numeric($data['price']['value'])) {
            msg('Price value is not numeric', 'error');
            return false;
        }
        
        /**
         * set net weight same as gross if not provided
         */
        
        if (!is_numeric($data['weight'])) $data['weight'] = $data['weight_gross'];
        
        /**
         * prepare core variety data
         */
         
        $variety_data = array();
        $variety_data['product_id'] = $data['product_id'];
        $variety_data['name'] = $data['name'];
        $variety_data['sku'] = $data['sku'];
        $variety_data['weight_gross'] = $data['weight_gross'];
        $variety_data['weight'] = $data['weight'];
        $variety_data['stock'] = $data['stock'];
        $variety_data['product_type_id'] = $data['product_type_id'];

        /**
         * insert
         */
        
        if($product_variety_id = $this->insertVariety($variety_data)) {
        
            require_once('models/ecommerce/ecommerce_price.php');
            
            $Price = new ecommerce_price();
            
            $price_data = $data['price'];
            $price_data['product_variety_id'] = $product_variety_id;
                    
            if($price_id = $Price->priceInsert($price_data)) {
                return $product_variety_id;
            } else {
                msg("Adding of price failed", "error");
                //it's still quite positive result :)
                return true;
            }
            
        } else {
            msg ("Can't add product variety. Are you sure SKU is not used by other product?", "error");
            return false;
        }
        
    }
    
    /**
     * update variety
     */
     
    function updateVariety($data) {
    
        if (is_array($data['other_data'])) $data['other_data'] = serialize($data['other_data']);
        $data['weight'] = $this->convertWeight($data['weight'], $this->conf['weight_units'], 'g');
        $data['weight_gross'] = $this->convertWeight($data['weight_gross'], $this->conf['weight_units'], 'g');
        $data['modified'] = date('c');
        
        if($id = $this->update($data)) {
            return $id;
        } else {
            return false;
        }
    }
    
    
    /**
     * duplication with ./ecommerce/ecommerce_delivery.php:
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
    
    /**
     * temporary implementation for bo/component/single_record_update
     */
    
    function updateSingleAttribute($attribute, $update_value, $id) {
    
        switch ($attribute) {
            case 'name':
                $variety_data = $this->detail($id);
                if ($variety_data) {
                    $variety_data['name'] = $update_value;
                    return $this->update($variety_data);
                }
            break;
            case 'sku':
                $variety_data = $this->detail($id);
                if ($variety_data) {
                    $variety_data['sku'] = $update_value;
                    return $this->update($variety_data);
                }
            break;
            case 'stock':
                $variety_data = $this->detail($id);
                if ($variety_data) {
                    $variety_data['stock'] = $update_value;
                    return $this->update($variety_data);
                }
            break;
        }
    }

    /**
     * update a record
     *
     * customer update function to sent notifications if stock level changes as per defined conditions
     *
     * @param array $data
     * @return integer
     */
     
    public function update($data = array())
    {
        $detail = $this->detail($data['id']);

        if (is_numeric($detail['stock']) && is_numeric($data['stock']) && $detail['stock'] != $data['stock'])
        {
            require_once('models/common/common_watchdog.php');
            $Watchdog = new common_watchdog();
            $Watchdog->checkWatchdog('back_in_stock_admin', $data['id'], $detail['stock'], $data['stock']);
            $Watchdog->checkWatchdog('out_of_stock_admin', $data['id'], $detail['stock'], $data['stock']);
            $Watchdog->checkWatchdog('back_in_stock_customer', $data['id'], $detail['stock'], $data['stock']);
        }

        return parent::update($data);
    }

    /**
     * get product variety list for given SKU or list of SKUs (separated by comma)
     */
     
    function getVarietyListForSKU($sku) {
    
        if (empty($sku)) return array();

        $sku_list = array();
        $sku_list_raw = ",";

        $skus = explode(",", $sku);
        foreach ($skus as $sku) {
            $sku = trim($sku);
            if (!empty($sku)) {
                $sku_list[] = "'" . pg_escape_string($sku) . "'";
                $sku_list_raw .= pg_escape_string($sku) . ",";
            }
        }
        if (count($sku_list) == 0) return array();

        $sku_list = implode(",", $sku_list);
        $varieties = $this->listing("sku in ($sku_list)", "position(',' || sku::text || ',' in '$sku_list_raw')");

        if (is_array($varieties)) {
            foreach ($varieties as $kv => $v) {
                $variety[$kv] = $this->getVarietyDetail($v['id']);
            }
        } else {
            $varieties = array();
        }

        return $varieties;
    }

}
