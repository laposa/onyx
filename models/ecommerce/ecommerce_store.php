<?php
/**
 * class ecommerce_store
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
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
	var $type;
	
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
	var $other_data;
	
	var $_metaData = array(
		'id'=>array('label' => '', 'validation' => 'int', 'required' => true),
		'title'=>array('label' => '', 'validation' => 'string', 'required' => true),
		'description'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'address'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'opening_hours'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'telephone'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'manager_name'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'email'=>array('label' => '', 'validation' => 'email', 'required' => false),
		'type'=>array('label' => '', 'validation' => 'int', 'required' => false),
		'coordinates_x'=>array('label' => '', 'validation' => 'int', 'required' => false),
		'coordinates_y'=>array('label' => '', 'validation' => 'int', 'required' => false),
		'latitude'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'longitude'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'created'=>array('label' => '', 'validation' => 'datetime', 'required' => false),
		'modified'=>array('label' => '', 'validation' => 'datetime', 'required' => false),
		'publish'=>array('label' => '', 'validation' => 'int', 'required' => false),
		'street_view_options'=>array('label' => '', 'validation' => 'string', 'required' => false),
		'other_data'=>array('label' => '', 'validation' => 'string', 'required' => false)
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
    type integer,
    coordinates_x integer,
    coordinates_y integer,
    latitude double precision,
    longitude double precision,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    publish smallint DEFAULT 0 NOT NULL,
    street_view_options text,
    other_data text
);
	";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration()
	{
	
		$conf = array();

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
    
    function getFilteredStoreList($taxonomy_id = false, $keyword = false, $order_by = false, $order_dir = false, $per_page = 25, $from = 0)
    {

    	$keyword = pg_escape_string(trim($keyword));

    	$where = '1 = 1';

    	//keyword
    	if (is_numeric($keyword)) $where .= " AND ecommerce_store.id = {$keyword}";
    	else if ($keyword != '') $where .= " AND (ecommerce_store.title ILIKE '%{$keyword}%' OR ecommerce_store.description ILIKE '%{$keyword}%' OR ecommerce_store.address ILIKE '%{$keyword}%')";

    	//taxonomy
    	if ($taxonomy_id > 0) $where .= " AND ecommerce_store.id IN (SELECT node_id FROM ecommerce_store_taxonomy WHERE taxonomy_tree_id = $taxonomy_id)";

		// order
		if ($order_by == 'title' || $order_by == 'modified') $order = "$order_by";
		else $order = "title";
		if ($order_dir == 'DESC') $order .= " DESC";
		else $order .= " ASC";

		// limits
		if (is_numeric($from)) $limit = "$from";
		else $limit = "0";
		if (is_numeric($per_page)) $limit .= ",$per_page";
		else $limit .= ",25";

		$records = $this->listing($where, $order, $limit);
		$count = $this->count($where);

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

			return array($records, $count);
		}

		return array(array(), $count);
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

}