<?php
/**
 * class ecommerce_product
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class ecommerce_product extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;
	/**
	 * @access private
	 */
	var $name;
	
	var $teaser;
	
	/**
	 * @access private
	 */
	var $description;

	/**
	 * @access private
	 */
	var $product_type_id;
	/**
	 * URL reference to manufacturer website,
	 * can be very long...
	 * @access private
	 */
	var $url;
	/**
	 * @access private
	 */
	var $priority;
	
	var $publish;
	
	var $other_data;
	
	var $modified;
	
	var $availability;
	
	var $name_aka;

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'name'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'teaser'=>array('label' => '', 'validation'=>'xhtml', 'required'=>false),
		'description'=>array('label' => '', 'validation'=>'xhtml', 'required'=>false),
		'product_type_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'url'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'availability'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'name_aka'=>array('label' => '', 'validation'=>'string', 'required'=>false)
	);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_product (
    id serial NOT NULL PRIMARY KEY,
    name character varying(255),
    teaser text,
    description text,
    product_type_id integer REFERENCES ecommerce_product_type ON UPDATE CASCADE ON DELETE CASCADE,
    url text,
    priority integer DEFAULT 0 NOT NULL,
    publish integer DEFAULT 0 NOT NULL,
    other_data text,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
	availability smallint NOT NULL DEFAULT 0,
	name_aka varchar(255)
);
		";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
	
		if (array_key_exists('ecommerce_product', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_product'];
		else $conf = array();

		if (!is_numeric($conf['gift_wrap_product_id'])) $conf['gift_wrap_product_id'] = 0; //set to numeric id > 0, to enable gift wrap option on checkout
		if (!is_numeric($conf['gift_voucher_product_id'])) $conf['gift_voucher_product_id'] = 0; //set to numeric id > 0, to enable gift vouchers
		
		return $conf;
	}
	
	/**
	 * insert product
	 */
	 
	public function insertProduct($data) {

		$data['priority'] = 0;
		$data['publish'] = 0;
		$data['modified'] = date('c');
		$data['availability'] = 0;

		if($id = $this->insert($data)) {
			return $id;
		} else {
			return false;
		}
		
	}
	
	/**
	 * insert full product
	 */
	 
	public function insertFullProduct($data) {
	
		/**
		 * check input values
		 */
			 
		if (trim($data['name']) == "") {
	 		msg('Product name is empty', 'error');
	 		return false;
	 	}
	 	
	 	if (!is_numeric($data['product_type_id'])) {
	 		msg('Product type id is not numeric', 'error');
	 		return false;
	 	}
	 	
	 	if (trim($data['variety']['name']) == "") {
	 		msg('Variety name is empty', 'error');
	 		return false;
	 	}
	 	
	 	if (trim($data['variety']['sku']) == "") {
	 		msg('Variety SKU is empty', 'error');
	 		return false;
	 	}
	 	
	 	if (!is_numeric($data['variety']['weight_gross'])) {
	 		msg('weight_gross is not numeric', 'error');
	 		return false;
	 	}
	 	
	 	if (!is_numeric($data['variety']['stock'])) {
	 		msg('Stock value is not numeric', 'error');
	 		return false;
	 	}
	 	
	 	if (trim($data['variety']['price']['currency_code']) == "") {
	 		msg('Currency code is empty', 'error');
	 		return false;
	 	}
	 	
	 	if (trim($data['variety']['price']['type']) == "") {
	 		msg('Price type', 'error');
	 		return false;
	 	}
	 	
		if (!is_numeric($data['variety']['price']['value'])) {
			msg('Price value is not numeric', 'error');
			return false;
		}
		
		/**
		 * prepare core product data
		 */
		
		$product_data = array();
		$product_data['name'] = $data['name'];
		$product_data['product_type_id'] = $data['product_type_id'];

		/**
		 * insert
		 */
				
		if ($product_id = $this->insertProduct($product_data)) {
		
			require_once('models/ecommerce/ecommerce_product_variety.php');
		
			$Product_variety = new ecommerce_product_variety();
		
			$data['variety']['product_id'] = $product_id;

			if($product_variety_id = $Product_variety->insertFullVariety($data['variety'])) {
			
				//return product id, not product variety id
				return $product_id;
				
			} else {
				return false;
			}
		} else {
			return false;
		}
	} 
	
	/**
	 * getDetail
	 */
	 
	public function getDetail($id) {
		
		if (!is_numeric($id)) return false;
		
		$product_detail = $this->detail($id);
		//other data
		$product_detail['other_data'] = unserialize($product_detail['other_data']);
	
		return $product_detail;
		
	}
	
	/**
	 * product detail
	 */
	 
	function productDetail($id) {
	
		$product_detail = $this->getDetail($id);
	
		if (is_array($product_detail)) {
			require_once('models/ecommerce/ecommerce_product_type.php');
			$ProductType = new ecommerce_product_type();
			$product_type_detail = $ProductType->detail($product_detail['product_type_id']);
			$product_detail['type'] = $product_type_detail;
			//better keep this...
			$product_detail['vat'] = $product_type_detail['vat'];
		
			return $product_detail;
		} else {
			msg("ecommercer_product.ProductDetail($id): can't get detail", 'error', 1);
			return false;
		}
	}
	
	/**
	 * get product detail
	 */
	
	function getProductDetail($product_id) {
	
		$product = $this->productDetail($product_id);
		$product['variety'] = $this->getProductVarietyList($product_id);
		
		return $product;
	}
	
	/**
	 * get product variety detail
	 */
	
	function getProductVarietyDetail($variety_id, $price_id = 0, $currency_code = GLOBAL_DEFAULT_CURRENCY) {
	
		if (!is_numeric($variety_id)) return false;
		
		require_once('models/ecommerce/ecommerce_product_variety.php');
		$ProductVariety = new ecommerce_product_variety();
		
		$variety = $ProductVariety->getVarietyDetail($variety_id, $price_id, $currency_code);

		return $variety;
	}
	
	/**
	 * get product variety list
	 */
	 
	function getProductVarietyList($product_id) {
	
		if (!is_numeric($product_id)) return false;
		
		require_once('models/ecommerce/ecommerce_product_variety.php');
		$ProductVariety = new ecommerce_product_variety();
		
		$varieties = $ProductVariety->listing("product_id = $product_id", 'priority DESC, id ASC');
			
		if (is_array($varieties)) {
			foreach ($varieties as $kv=>$v) {
				$variety[$kv] = $ProductVariety->getVarietyDetail($v['id']);
			}
			
			return $variety;
		} else {
			msg("Product id $product_id has no varieties", 'error', 1);
			return false;
		}
	}
	
	/**
	 * get product detail by variety id
	 */
	 
	function getProductDetailByVarietyId($variety_id, $price_id = 0) {
		
		$variety_data = $this->getProductVarietyDetail($variety_id, $price_id);
		$product_data = $this->ProductDetail($variety_data['product_id']);
		
		$result = $product_data;
		$result['variety'] = $variety_data;
		
		return $result;
	}
	
	/**
	 * basic search
	 */
	 
    function search($q) {
		//$q = htmlspecialchars($q);
		$q = addslashes($q);
    	$q = "%$q%";
    	$result = $this->listing("name ILIKE '$q' OR 
    	teaser ILIKE '$q' OR 
    	description ILIKE '$q'");
    	return $result;
    }
    
    /**
     * get simple product list
     */
     
	function getProductList() {
	
		$product_list = array();
	
			$products = $this->listing('', 'priority DESC, id ASC');
			foreach ($products as $kp=>$p) {
				$product_list[$kp] = $this->ProductDetail($p['id']);
				$product_list[$kp]['variety'] = $this->getProductVarietyList($p['id']);
			}
		
		return $product_list;
	}
    
    
    /**
     * get filtered product list
     *
     */
    
    function getFilteredProductList($filter = null, $currency_code = GLOBAL_DEFAULT_CURRENCY, $price_type = 'common') {
    	
    	//sanitize input
    	$filter['keyword'] = pg_escape_string(trim($filter['keyword']));//addslashes or pg_escape_string
    	
    	$add_to_where = '';
    	
    	if (is_array($filter)) {
    	
	    	//node_id
	    	if (is_array($filter['node_id'])) {
	    		if ($node_id > 0 && is_numeric($node_id)) $add_to_where = " AND product.id IN (SELECT content FROM common_node WHERE node_group = 'page' AND node_controller = 'product' AND parent = $node_id)";
	    		$x="node.node_group = 'page' AND node.node_controller = 'product' AND node.parent = $node_id AND node.publish = 1";
	    	}
	    	
	    	//keyword
	    	if (is_numeric($filter['keyword'])) $add_to_where .= " AND product.id = {$filter['keyword']} OR variety.id = {$filter['keyword']}";
	    	else if ($filter['keyword'] != '') $add_to_where .= " AND (variety.sku ILIKE '%{$filter['keyword']}%' OR product.name ILIKE '%{$filter['keyword']}%')";
	    	else $add_to_where .= " AND (image.role != 'RTE' OR image.role IS NULL) "; //use image filter only when not empty keyword (it allows to find product with one image role RTE)
	    	
	    	//stock value
	    	if (is_numeric($filter['stock']) && $filter['stock'] >= 0) $add_to_where .= " AND variety.stock < {$filter['stock']}";
	    	
	    	//publish filter and deprecated disable/enabled option
	    	if ($filter['publish'] === 0 || $filter['disabled'] == 'disabled') $add_to_where .= " AND product.publish = 0";
	    	else if ($filter['publish'] === 1  || $filter['disabled'] == 'enabled') $add_to_where .= " AND product.publish = 1 AND variety.publish = 1";
	    	
	    	//image role
	    	if ($filter['image_role']) {
	    		$filter['image_role'] = pg_escape_string($filter['image_role']);
	    		$add_to_where .= " AND image.role = '{$filter['image_role']}'";
	    	}
	    	
	    	//taxonomy
	    	if ($filter['taxonomy_json']) {
	    	
	    		$taxonomy_list = json_decode($filter['taxonomy_json']);

	    		if (is_array($taxonomy_list)) {
	    		
	    			$taxonomy_method = "AND";
	    			
	    			if ($taxonomy_method == 'OR') {
		    			/**
		    			 * OR
		    			 */
		    			$add_to_where .= " AND ( taxonomy.taxonomy_tree_id IN (";
		    			$add_to_where .= join(",", $taxonomy_list);
	    				$add_to_where .= ")) ";
    				} else {
	    				/**
	    				 * AND
	    				 */
						/*
						SELECT t1.node_id  FROM ecommerce_product_taxonomy  t1
						LEFT JOIN ecommerce_product_taxonomy t2 ON (t1.node_id = t2.node_id)
						WHERE t1.taxonomy_tree_id = 7 AND t2.taxonomy_tree_id = 14
						
						SELECT t1.node_id  FROM ecommerce_product_taxonomy  t1
						LEFT JOIN ecommerce_product_taxonomy t2 ON (t1.node_id = t2.node_id)
						LEFT JOIN ecommerce_product_taxonomy t3 ON (t1.node_id = t3.node_id)
						WHERE t1.taxonomy_tree_id = 7 AND t2.taxonomy_tree_id = 14 AND t3.taxonomy_tree_id = 56
						*/

	    				$add_to_where .= " AND ( product_id IN (
	    				SELECT t0.node_id  FROM ecommerce_product_taxonomy  taxonomy ";
						foreach ($taxonomy_list as $key=>$item) {
							$add_to_where .= " LEFT JOIN ecommerce_product_taxonomy t$key ON (t$key.node_id = taxonomy.node_id) ";
		    			}
		    			$add_to_where .= " WHERE ";
		    			foreach ($taxonomy_list as $key=>$item) {
		    				if ($key > 0) $add_to_where .= " AND ";
		    				$add_to_where .= "t$key.taxonomy_tree_id = $item";
		    			}
	    				$add_to_where .= ")) ";
    				}
    			}
    		}
    		
    		/**
    		 * show only some particular products defined by their id
    		 */
    		
    		if (is_array($filter['product_id_list'])) {
    			
    			$product_id_list = array();
    			
    			//check for numeric values
    			foreach ($filter['product_id_list'] as $product_id) {
    				if (is_numeric($product_id)) $product_id_list[] = $product_id;
    			}
    			
    			if (count($product_id_list) > 0) {
    				$id_list = join(",", $product_id_list);
    				$add_to_where .= " AND product_id IN ($id_list) ";
    			}
    		}
    	}
    	
    	if ($currency_code != GLOBAL_DEFAULT_CURRENCY) {
			require_once('models/international/international_currency_rate.php');
			$Currency = new international_currency_rate();
			$exchange_rate = $Currency->getLatestExchangeRate(GLOBAL_DEFAULT_CURRENCY, $currency_code);
		} else {
			$exchange_rate = 1;
		}

    	
		$sql = "
		SELECT DISTINCT ON (variety.id) price.value * (100 + ecommerce_product_type.vat)/100 * $exchange_rate  AS price, 
		price.value AS price_net,
		price.date AS price_date,
		ecommerce_product_type.vat AS vat_rate,
		variety.id AS variety_id, 
		variety.name AS variety_name, 
		variety.sku,
		variety.weight AS weight_net,
		variety.weight_gross AS weight_gross,
		variety.stock,
		variety.publish AS variety_publish,
		product.id AS product_id, 
		product.name AS product_name, 
		product.teaser AS product_teaser, 
		product.publish,
		product.modified,
		product.priority,
		image.src AS image_src,
		image.title AS image_title,
		image.priority AS image_priority,
		image.id,
		count(review.id) AS review_count, 
		avg(review.rating) AS review_rating, 
		(SELECT array_to_string(array_agg(taxonomy.taxonomy_tree_id), ',') FROM ecommerce_product_taxonomy taxonomy WHERE taxonomy.node_id = product.id) AS taxonomy 
		FROM ecommerce_product product 
		LEFT OUTER JOIN ecommerce_product_type ON (ecommerce_product_type.id = product.product_type_id) 
		LEFT OUTER JOIN ecommerce_product_variety variety ON (variety.product_id = product.id)
		LEFT OUTER JOIN ecommerce_price price ON (price.product_variety_id = variety.id) 
		LEFT OUTER JOIN ecommerce_product_image image ON (image.node_id = product.id) 
		LEFT OUTER JOIN ecommerce_product_review review ON (review.node_id = product.id AND review.publish = 1)
		WHERE price.type = '$price_type'
		$add_to_where
		GROUP BY variety.id, product.id,  price.value, ecommerce_product_type.vat, product.name, product.teaser, product.priority, variety.name,
variety.stock, price.date, product.publish, product.modified, variety.sku, variety.weight, variety.weight_gross, variety.publish, image.src, image.title, image.priority, image.id
		ORDER BY variety_id ASC, price.date DESC, image_priority DESC, image.id ASC";
    	
		//msg ($sql);
		
		$records = $this->executeSql($sql);
		
		if (is_array($records)) {
			
			//change node_id to productHomepage
			foreach($records as $k=>$record) {
				$homepage = $this->getProductHomepage($record['product_id']);
				$records[$k]['node_id'] = $homepage['id'];
				$records[$k]['node_publish'] = $homepage['publish'];
			}
			
			return $records;

		} else {
			
			return false;
		}
    }

	
	/**
	 * depricated function
	 * Optimized product list in node
	 *
	 * @param unknown_type $node_id
	 * @param unknown_type $currency_code
	 * @return unknown
	 */

	function getProductVarietyListInNode($node_id, $currency_code = GLOBAL_DEFAULT_CURRENCY, $price_type = 'common') {
		
		if (!is_numeric($node_id)) {
			msg("Product->getProductVarietyListInNode(): node_id is not numeric");
			return false;
		}
		
		if ($currency_code != GLOBAL_DEFAULT_CURRENCY) {
			require_once('models/international/international_currency_rate.php');
			$Currency = new international_currency_rate();
			$exchange_rate = $Currency->getLatestExchangeRate(GLOBAL_DEFAULT_CURRENCY, $currency_code);
		} else {
			$exchange_rate = 1;
		}
		
		$sql = "
		SELECT DISTINCT ON (variety.id) price.value * (100 + ecommerce_product_type.vat)/100 * $exchange_rate  AS price, 
		price.date, 
		variety.id AS variety_id, 
		variety.name AS variety_name, 
		variety.stock, 
		node.id AS node_id, 
		node.content, 
		node.publish AS node_publish, 
		product.id AS product_id, 
		product.name AS product_name, 
		product.teaser AS product_teaser, 
		product.priority, 
		image.src AS image_src, 
		image.title AS image_title, 
		image.priority AS image_priority, 
		image.id, 
		count(review.id) AS review_count, 
		avg(review.rating) AS review_rating,
		(SELECT array_to_string(array_agg(taxonomy.taxonomy_tree_id), ',') FROM ecommerce_product_taxonomy taxonomy WHERE taxonomy.node_id = product.id) AS taxonomy
		FROM common_node node
		LEFT OUTER JOIN ecommerce_product product ON (product.id = node.content::int)
		LEFT OUTER JOIN ecommerce_product_type ON (ecommerce_product_type.id = product.product_type_id) 
		LEFT OUTER JOIN ecommerce_product_variety variety ON (variety.product_id = product.id)
		LEFT OUTER JOIN ecommerce_price price ON (price.product_variety_id = variety.id) 
		LEFT OUTER JOIN ecommerce_product_image image ON (image.node_id = product.id)
		LEFT OUTER JOIN ecommerce_product_review review ON (review.node_id = product.id AND review.publish = 1)
		WHERE node.node_group = 'page' AND node.node_controller = 'product' AND node.parent = $node_id AND node.publish = 1 AND variety.publish = 1 AND image.role != 'RTE' AND price.type = '$price_type'
		GROUP BY variety.id, product.id, node.id, node.content, price.value, ecommerce_product_type.vat, product.name, product.teaser, product.priority, variety.name,
variety.stock, price.date, image.src, image.title, image.priority, image.id, node.publish
		ORDER BY variety_id ASC, price.date DESC, image_priority DESC, image.id ASC";

		//msg($sql);
		
		$records = $this->executeSql($sql);
		
		if (is_array($records)) {
			
			//change node_id to productHomepage
			foreach($records as $k=>$record) {
				$homepage = $this->getProductHomepage($record['product_id']);
				$records[$k]['node_id'] = $homepage['id'];
			}
 			//print_r($records);
			return $records;
		
		} else {
			
			return false;
		}
	}


    /**
     * Find product homepage
     * use simple cache
	 * TODO: find by product_id id node.content when multiple product nodes functionality is removed
	 * multiple product node functionality is depricated in favour of using taxonomy
     */
     
    function getProductHomepage($product_id) {
    
    	require_once('models/common/common_node.php');
    	$Node = new common_node();
    	
    	if (is_numeric($product_id)) {
    		if (!is_array($this->_cache_product_in_node)) $this->_cache_product_in_node = $Node->listing("node_group = 'page' AND node_controller = 'product'", 'id ASC');
    		foreach ($this->_cache_product_in_node as $p) {
    			//return first in list - it's homepage
    			if ($p['content'] == $product_id) return $p;
    		}
    		//return true is search complete, but no page found
    		return true;
    	} else {
    		return false;
    	}

    }
    
    
	/**
	 * Get list of most popular product
	 * 
	 * @todo count all product_varieies of one product together
	 *
	 * @param unknown_type $order
	 * @param unknown_type $limit
	 * @package mixed $customer_id
	 * @return mixed
	 */
	 
	function getMostPopularProducts($order = 'DESC', $limit = 10, $customer_id = false, $period_limit = 30) {
		
		if (is_numeric($customer_id)) $add_sql = "AND basket.customer_id = $customer_id";
		else $add_sql = '';
		
		if (is_numeric($period_limit) && $period_limit > 0) {
			$add_sql .= " AND extract('days' FROM (now() - basket.created) ) < $period_limit";
		}
		
		
		$sql = "
		SELECT DISTINCT product_variety.product_id AS product_id, product_variety_id, count(product_variety_id) AS count, product.name AS product_name, product_variety.name AS variety_name 
		FROM ecommerce_basket_content basket_content
		LEFT OUTER JOIN ecommerce_product_variety product_variety ON (product_variety.id = product_variety_id)
		LEFT OUTER JOIN ecommerce_product product ON (product.id = product_variety.product_id)
		LEFT OUTER JOIN ecommerce_basket basket ON (basket.id = basket_content.basket_id)
		WHERE product.publish = 1 $add_sql
		GROUP BY product_id, product_variety_id, product_name, variety_name 
		ORDER BY count $order LIMIT $limit";
		
		return $this->executeSql($sql);
		
	}
	
	
	/**
	 * get popularity
	 */
	 
	function getPopularity($product_id) {
		
		if (!is_numeric($product_id)) {
			msg("Product->getPopularity: product_id ($product_id) is not numeric", 'error');
			return false;
		}
		
		$sql = "
		SELECT count(ecommerce_basket_content.product_variety_id) FROM ecommerce_basket_content 
		WHERE ecommerce_basket_content.product_variety_id IN (
			SELECT ecommerce_product_variety.id FROM ecommerce_product_variety 
			WHERE ecommerce_product_variety.product_id = $product_id
		)
		"; 
		
		$records = $this->executeSql($sql);
		
		if (is_array($records)) {
		
			return $records[0]['count'];
		
		} else {
			return false;
		}
		
	}

	/**
	 * find product in node
	 */
	 
    function findProductInNode($product_id) {
    
    	require_once('models/common/common_node.php');
    	$Node = new common_node();
    	
    	if (is_numeric($product_id)) {
    		$current = $Node->listing("node_group = 'page' AND node_controller = 'product' AND content = '{$product_id}'", 'id ASC');
    		return $current;
    	} else {
    		msg("ecommerce_product.findProductInNode: product id is not numeric", 'error');
    		return false;
    	}
    }
    
	/**
	 * get unfinished products
	 */
    
    function getUnfinishedProduct() {
    	
    	$sql = "SELECT * FROM ecommerce_product p WHERE p.id NOT IN (SELECT product_id FROM ecommerce_product_variety)";
    	
    	return $this->executeSql($sql);
    	
    }
    
    /**
     * product delete
     */
    
    function productDelete($id) {
    	//TODO check if this product haven't been inserted into the basket (should be OK with database references)
    	if ($this->delete($id)) return true;
		else return false;
    }
    
    /**
	 * get taxonomy relation
	 */
	 
	function getTaxonomyForProduct($product_id) {
	
		if (!is_numeric($product_id)) return false;
		
		require_once('models/ecommerce/ecommerce_product_taxonomy.php');
		$Taxonomy = new ecommerce_product_taxonomy();
		
		$relations = $Taxonomy->getRelationsToProduct($product_id);
		
		return $relations;
	}
	
	/**
	 * getRelatedTaxonomy
	 */
	 
	public function getRelatedTaxonomy($product_id) {
		
		if (!is_numeric($product_id)) return false;
		
		require_once('models/common/common_taxonomy.php');
		$Taxonomy = new common_taxonomy();
		$related_taxonomy = $Taxonomy->getRelatedTaxonomy($product_id, 'ecommerce_product_taxonomy');
				
		return $related_taxonomy;
	}
}
