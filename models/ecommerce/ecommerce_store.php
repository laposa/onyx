<?php
/**
 * class ecommerce_store
 *
 * Copyright (c) 2013-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class ecommerce_store extends Onxshop_Model {

	/**
	 * @access public
	 */
	var $id;

	/**
	 * @access public
	 */
	var $title;
	
	/**
	 * @access public
	 */
	var $description;
	
	/**
	 * @access public
	 */
	var $address;

	/**
	 * @access public
	 */
	var $opening_hours;

	/**
	 * @access public
	 */
	var $telephone;
	
	/**
	 * @access public
	 */
	var $manager_name;
	
	/**
	 * @access public
	 */
	var $email;
	
	/**
	 * @access public
	 */
	var $url;
	
	/**
	 * @access public
	 */
	var $type_id;
	
	/**
	 * @access public
	 */
	var $coordinates_x;
	
	/**
	 * @access public
	 */
	var $coordinates_y;
	
	/**
	 * @access public
	 */
	var $latitude;
	
	/**
	 * @access public
	 */
	var $longitude;
	
	/**
	 * @access public
	 */
	var $created;
	
	/**
	 * @access public
	 */
	var $modified;
	
	/**
	 * @access public
	 */
	var $publish;
	
	/**
	 * @access public
	 */
	var $street_view_options;
	
	/**
	 * @access public
	 */
	var $other_data;
	
	/**
	 * @access public
	 */
	var $country_id;
	
	/**
	 * @access public
	 */
	var $address_name;
	
	/**
	 * @access public
	 */
	var $address_line_1;
	
	/**
	 * @access public
	 */
	var $address_line_2;
	
	/**
	 * @access public
	 */
	var $address_line_3;
	
	/**
	 * @access public
	 */
	var $address_city;
	
	/**
	 * @access public
	 */
	var $address_county;
	
	/**
	 * @access public
	 */
	var $address_post_code;
	
	/**
	 * @access public
	 */
	var $code;
	
	var $_metaData = array(
		'id'=>array('label' => '', 'validation' => 'int', 'required' => true),
		'title'=>array('label' => '', 'validation' => 'string', 'required' => true),
		'description'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'address'=>array('label' => 'Full address as one input box', 'validation' => 'string', 'required' => false),
		'opening_hours'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'telephone'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'manager_name'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'email'=>array('label' => '', 'validation' => 'email', 'required' => false),
		'url'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'type_id'=>array('label' => '', 'validation' => 'int', 'required' => false),
		'coordinates_x'=>array('label' => '', 'validation' => 'int', 'required' => false),
		'coordinates_y'=>array('label' => '', 'validation' => 'int', 'required' => false),
		'latitude'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'longitude'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'created'=>array('label' => '', 'validation' => 'datetime', 'required' => false),
		'modified'=>array('label' => '', 'validation' => 'datetime', 'required' => false),
		'publish'=>array('label' => '', 'validation' => 'int', 'required' => false),
		'street_view_options'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'other_data'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'country_id'=>array('label' => 'Country', 'validation'=>'int', 'required'=>false),
		'address_name'=>array('label' => 'Name', 'validation'=>'string', 'required'=>false),
		'address_line_1'=>array('label' => 'Address line 1', 'validation'=>'string', 'required'=>false),
		'address_line_2'=>array('label' => 'Address line 2', 'validation'=>'string', 'required'=>false),
		'address_line_3'=>array('label' => 'Address line 3', 'validation'=>'string', 'required'=>false),
		'address_city'=>array('label' => 'City', 'validation'=>'string', 'required'=>false),
		'address_county'=>array('label' => 'County', 'validation'=>'string', 'required'=>false),
		'address_post_code'=>array('label' => 'Post code', 'validation'=>'string', 'required'=>false),
		'code'=>array('label' => 'Reference code', 'validation'=>'string', 'required'=>false)
	);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_store (
    id integer DEFAULT nextval('ecommerce_store_id_seq'::regclass) NOT NULL,
    title character varying(255),
    description text,
    address text,
    opening_hours text,
    telephone character varying(255),
    manager_name character varying(255),
    email character varying(255),
    url character varying(512),
    type_id REFERENCES ecommerce_store_type ON UPDATE CASCADE ON DELETE RESTRICT,
    coordinates_x integer,
    coordinates_y integer,
    latitude double precision,
    longitude double precision,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    publish smallint DEFAULT 0 NOT NULL,
    street_view_options text,
    other_data text,
    country_id int REFERENCES international_country ON UPDATE CASCADE ON DELETE RESTRICT,
    address_name varchar(255),
    address_line_1 varchar(255),
    address_line_2 varchar(255),
    address_line_3 varchar(255),
    address_city varchar(255),
    address_county varchar(255),
    address_post_code varchar(255),
    code varchar(255)
);
	";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration()
	{
	
		if (array_key_exists('ecommerce_store', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_store'];
		else $conf = array();
		
		/**
		 * default values
		 */
		 
		if (!$conf['latitude']) $conf['latitude'] = 53.344189;
		if (!$conf['longitude']) $conf['longitude'] = -6.264478;

		return $conf;
	}



	/**
	 * insert store
	 */
	function insertStore($data)
	{
		$data['publish'] = 0;
		$data['created'] = date('c');
		$data['modified'] = date('c');

		if ($id = $this->insert($data)) {
			return $id;
		} else {
			return false;
		}
	}



    /**
     * get filtered store list
     *
     */
    
    function getFilteredStoreList($taxonomy_id = false, $keyword = false, $type_id = 0, $order_by = false, $order_dir = false, $per_page = false, $from = false)
    {
    	$where = $this->prepareStoreFilteringSql($taxonomy_id, $keyword, $type_id);

		// order
		if ($order_by == 'title' || $order_by == 'modified') $order = "$order_by";
		else $order = "title";
		if ($order_dir == 'DESC') $order .= " DESC";
		else $order .= " ASC";

		// limits
		if (is_numeric($from)) $limit = "$from";
		else $limit = "0";
		if (is_numeric($per_page)) $limit .= ",$per_page";
		else $limit = false;

		$records = $this->listing($where, $order, $limit);

		if (is_array($records)) {

			require_once('models/ecommerce/ecommerce_store_image.php');
			$Image = new ecommerce_store_image();

			foreach ($records as $i => $item) {
				$images = $Image->listFiles($item['id']);
				if (count($images) > 0) {
					$records[$i]['image_src'] = $images[0]['src'];
					$records[$i]['image_title'] = $images[0]['title'];
				}
			}

			return $records;
		}

		return array();
    }

    /**
     * get filtered store list
     *
     */
    
    function getFilteredStoreCount($taxonomy_id = false, $keyword = false, $type_id = 0)
    {
    	$where = $this->prepareStoreFilteringSql($taxonomy_id, $keyword, $type_id);
		return $this->count($where);
    }

    /**
     * prepareStoreFilteringSql
     */

    private function prepareStoreFilteringSql($taxonomy_id, $keyword, $type_id)
    {
    	$sql = '1 = 1';

    	$keyword = pg_escape_string(trim($keyword));

    	//keyword
    	if (is_numeric($keyword)) $sql .= " AND ecommerce_store.id = {$keyword}";
    	else if ($keyword != '') $sql .= " AND (ecommerce_store.title ILIKE '%{$keyword}%' OR ecommerce_store.description ILIKE '%{$keyword}%' OR ecommerce_store.address ILIKE '%{$keyword}%')";

    	//type
    	if (is_numeric($type_id) && $type_id > 0) $sql .= " AND ecommerce_store.type_id = $type_id";
    	//taxonomy
    	if ($taxonomy_id > 0) $sql .= " AND ecommerce_store.id IN (SELECT node_id FROM ecommerce_store_taxonomy WHERE taxonomy_tree_id = $taxonomy_id)";

    	return $sql;
    }

    function getStoreImage($store_id) {

		require_once('models/ecommerce/ecommerce_store_image.php');
		$Image = new ecommerce_store_image();

		$images = $Image->listFiles($store_id);
		if (count($images) > 0) return $images[0]['src'];

		return false;
    }

    /**
     * Find store homepage
     * use simple cache
     */

    function getStoreHomepage($store_id) {

    	require_once('models/common/common_node.php');
    	$Node = new common_node();

    	if (is_numeric($store_id)) {
    		if (!is_array($this->_cache_store_in_node)) $this->_cache_store_in_node = $Node->listing("node_group = 'page' AND node_controller = 'store'", 'id ASC');
    		foreach ($this->_cache_store_in_node as $p) {
    			//return first in list - it's homepage
    			if ($p['content'] == $store_id) return $p;
    		}
    		//return true is search complete, but no page found
    		return true;
    	} else {
    		return false;
    	}

    }


	/**
	 * find store in node
	 */
	 
    function findStoreInNode($store_id) {
    
    	require_once('models/common/common_node.php');
    	$Node = new common_node();
    	
    	if (is_numeric($store_id)) {
    		$current = $Node->listing("node_group = 'page' AND node_controller = 'store' AND content = '{$store_id}'", 'id ASC');
    		return $current;
    	} else {
    		msg("ecommerce_store.findStoreInNode: store id is not numeric", 'error');
    		return false;
    	}
    }

	/**
	 * find store by node_id
	 */
	 
    function findStoreByNode($node_id) {
    
    	require_once('models/common/common_node.php');
    	$Node = new common_node();
    	
    	if (is_numeric($node_id)) {

    		$nodes = $Node->listing("node_group = 'page' AND node_controller = 'store' AND id = '{$node_id}'", 'id ASC');
    		
    		$store_id = $nodes[0]['content'];
    		if (is_numeric($store_id) && $store_id > 0) return $this->detail($store_id);

    	} else {
    		msg("ecommerce_store.findStoreByNode: node id is not numeric", 'error');
    	}

   		return false;
 	}


 	/**
 	 * Find nearest store that has specific taxonomy_id
 	 * @param  int $store_id    Home store to which the distance is measured
 	 * @param  int $taxonomy_id Required facility taxnomy_id
 	 * @return int store_id
 	 */
 	function findNearestStoreWithFacility($store_id, $taxonomy_id)
 	{
 		if (!is_numeric($store_id)) return false;
 		if (!is_numeric($taxonomy_id)) return false;

 		$list = $this->listing("id IN (SELECT node_id FROM ecommerce_store_taxonomy WHERE taxonomy_tree_id = $taxonomy_id)");
 		$store = $this->detail($store_id);

 		$nearest = 99999;

 		foreach ($list as $item) {
 			$distance = $this->distance($item['latitude'], $item['longitude'], $store['latitude'], $store['longitude']);
 			if ($distance < $nearest) {
 				$nearest = $distance;
 				$store_id = $item['id'];
 			}
 		}

 		return $store_id;
 	}


	/**
	 * Get distance between two points on sphere (in km)
	 * http://en.wikipedia.org/wiki/Haversine_formula
	 * 
	 * @param  float $lat1 First point latitude
	 * @param  float $lng1 First point longitude
	 * @param  float $lat2 Second point latitude
	 * @param  float $lng2 Second point longitude
	 * @return float
	 */
	public static function distance($lat1, $lng1, $lat2, $lng2)
	{
		$earth_radius = 6371;

		$sin_lat = sin(deg2rad($lat2  - $lat1) / 2.0);
		$sin2_lat = $sin_lat * $sin_lat;

		$sin_lng = sin(deg2rad($lng2 - $lng1) / 2.0);
		$sin2_lng = $sin_lng * $sin_lng;

		$cos_lat1 = cos($lat1);
		$cos_lat2 = cos($lat2);

		$sqrt = sqrt($sin2_lat + ($cos_lat1 * $cos_lat2 * $sin2_lng));

		$distance = 2.0 * $earth_radius * asin($sqrt);

		return $distance;
	}
	
	/**
	 * getRelatedTaxonomy
	 */
	 
	public function getRelatedTaxonomy($store_id) {
		
		if (!is_numeric($store_id)) return false;
		
		require_once('models/common/common_taxonomy.php');
		$Taxonomy = new common_taxonomy();
		$related_taxonomy = $Taxonomy->getRelatedTaxonomy($store_id, 'ecommerce_store_taxonomy');
		
		return $related_taxonomy;
	}

	/**
	 * getDataForNoticesReport
	 */

	public function getDataForNoticesReport($date_from, $date_to)
	{

		// set to last 7 days if no dates given
		if (!isValidDate($date_from)) $date_from = date("Y-m-d", time() - 3600 * 24 * 7);
		if (!isValidDate($date_to)) $date_to = date("Y-m-d", time());

		// add quotes and escape
		$date_from = $this->db->quote($date_from);
		$date_to = $this->db->quote($date_to);

		$sql = "SELECT 
				notice.id AS id,
				notice.created AS created,
				notice.modified AS modified,
				notice.other_data AS other_data,
				notice.publish AS publish,
				store.id AS store_id,
				store.code AS store_code,
				store.title AS store_title,
				store.manager_name AS store_manager_name,
				store.email AS store_email
			FROM common_node AS notice
			LEFT JOIN common_node AS parent ON parent.id = notice.parent
			LEFT JOIN ecommerce_store AS store ON store.id = parent.content::int
			WHERE notice.node_controller = 'notice' AND notice.created BETWEEN $date_from AND $date_to
		";

		$records = $this->executeSql($sql);

		$result = array();
		foreach ($records as $record) {

			$item = array();
			$data = unserialize($record['other_data']);

			$item['Web Store Id'] = $record['store_id'];
			$item['Store Code'] = $record['store_code'];
			$item['Store Title'] = $record['store_title'];
			$item['Store Manager'] = $record['store_manager_name'];
			$item['Store Email'] = $record['store_email'];
			$item['Notice Id'] = $record['id'];
			$item['Notice Created'] = $record['created'];
			$item['Is Published'] = $record['publish'] ? 'yes' : 'no';
			$item['Notice Text'] = $data['text'];
			$item['Visible From'] = $data['visible_from'];
			$item['Visible To'] = $data['visible_to'];
			$item['Image'] = $data['image'] ? 'yes' : 'no';

			$result[] = $item;
		}

		return $result;
	}

}
