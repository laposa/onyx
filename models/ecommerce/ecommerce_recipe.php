<?php
/**
 * class ecommerce_recipe
 *
 * Copyright (c) 2013-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class ecommerce_recipe extends Onxshop_Model {

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
	var $instructions;

	/**
	 * @access public
	 */
	var $video_url;

	/**
	 * @access public
	 */
	var $serving_people;
	
	/**
	 * @access public
	 */
	var $preparation_time;
	
	/**
	 * @access public
	 */
	var $cooking_time;
	
	/**
	 * @access public
	 */
	var $priority;
	
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
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'xhtml', 'required'=>false),
		'instructions'=>array('label' => '', 'validation'=>'xhtml', 'required'=>false),
		'video_url'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'serving_people'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'preparation_time'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'cooking_time'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'other_data'=>array('label' => '', 'validation'=>'string', 'required'=>false)
	);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_recipe (
    id serial PRIMARY KEY NOT NULL,
    title character varying(255),
    description text,
    instructions text,
    video_url text,
    serving_people integer,
    preparation_time integer,
    cooking_time integer,
    priority integer,
    created timestamp without time zone,
    modified timestamp without time zone DEFAULT now(),
    publish smallint,
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
	
		if (array_key_exists('ecommerce_recipe', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_recipe'];
		else $conf = array();
		
		return $conf;
	}

	/**
	 * getDetail
	 */
	 
	public function getDetail($id) {
		
		$data = $this->detail($id);
		
		if (is_array($data)) {
			// handle other_data
			$data['other_data'] = unserialize($data['other_data']);
		}
		
		return $data;
		
	}


	/**
	 * insert recipe
	 */
	function insertRecipe($data)
	{
		$data['priority'] = 0;
		$data['publish'] = 0;
		$data['created'] = date('c');
		$data['serving_people'] = 4;

		// handle other_data
		if (is_array($data['other_data'])) $data['other_data'] = serialize($data['other_data']);
		
		if ($id = $this->insert($data)) {
			return $id;
		} else {
			return false;
		}
	}

	/**
	 * update recipe
	 */
	function updateRecipe($data)
	{
		// set values
		if (!isset($data['publish'])) $data['publish'] = 0;
		$data['modified'] = date('c');
			
		// make sure values are int
		$data['serving_people'] = (int)$data['serving_people'];
		$data['preparation_time'] = (int)$data['preparation_time'];
		$data['cooking_time'] = (int)$data['cooking_time'];
			
		// handle other_data
		$data['other_data'] = serialize($data['other_data']);

		if ($id = $this->update($data)) {
		
			// update node info (if exists)
			$recipe_homepage = $this->getRecipeHomepage($id);
		
			if (is_array($recipe_homepage) && count($recipe_homepage) > 0) {
			
				$recipe_homepage['publish'] = $data['publish'];
				
				require_once('models/common/common_node.php');
				$Node = new common_node();
				
				$Node->nodeUpdate($recipe_homepage);
				
			}
			
			return $id;
		
		} else {
		
			return false;
		
		}
	}

    /**
     * get filtered recipe list
     *
     */
    
    function getFilteredRecipeList($taxonomy_id = false, $keyword = false, $order_by = false, $order_dir = false, $per_page = 25, $from = 0)
    {

    	$keyword = pg_escape_string(trim($keyword));

    	$where = '1 = 1';

    	//keyword
    	if (is_numeric($keyword)) $where .= " AND ecommerce_recipe.id = {$keyword}";
    	else if ($keyword != '') $where .= " AND (ecommerce_recipe.title ILIKE '%{$keyword}%' OR ecommerce_recipe.description ILIKE '%{$keyword}%' OR ecommerce_recipe.instructions ILIKE '%{$keyword}%')";

    	//taxonomy
    	if ($taxonomy_id > 0) $where .= " AND ecommerce_recipe.id IN (SELECT node_id FROM ecommerce_recipe_taxonomy WHERE taxonomy_tree_id = $taxonomy_id)";

		// order
		if (in_array($order_by, array('title', 'created', 'modified', 'priority', 'share_counter'))) $order = "$order_by";
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

			require_once('models/ecommerce/ecommerce_recipe_image.php');
			$Image = new ecommerce_recipe_image();

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


    /**
     * Find recipe homepage
     * use simple cache
     */
     
    function getRecipeHomepage($recipe_id) {
    
    	require_once('models/common/common_node.php');
    	$Node = new common_node();
    	
    	if (is_numeric($recipe_id)) {
    		if (!is_array($this->_cache_recipe_in_node)) $this->_cache_recipe_in_node = $Node->listing("node_group = 'page' AND node_controller = 'recipe'", 'id ASC');
    		foreach ($this->_cache_recipe_in_node as $p) {
    			//return first in list - it's homepage
    			if ($p['content'] == $recipe_id) return $p;
    		}
    		//return true is search complete, but no page found
    		return true;
    	} else {
    		return false;
    	}

    }


	/**
	 * find recipe in node
	 */
	 
    function findRecipeInNode($recipe_id) {
    
    	require_once('models/common/common_node.php');
    	$Node = new common_node();
    	
    	if (is_numeric($recipe_id)) {
    		$current = $Node->listing("node_group = 'page' AND node_controller = 'recipe' AND content = '{$recipe_id}'", 'id ASC');
    		return $current;
    	} else {
    		msg("ecommerce_recipe.findProductInNode: recipe id is not numeric", 'error');
    		return false;
    	}
    }


    /**
     * getRecipeListForTaxonomy
     *
     * list recipes for given taxonomy_ids
     * each item contains
     *    - main image details as 'image' field
     *    - page details as 'page' field
     *
     * @param array $taxonomy_ids
     * @param string $sort_by
     * @param string $sort_direction
     * @param int $limit_from
     * @param int $limit_per_page
	 * @return array
     */
    function getRecipeListForTaxonomy($taxonomy_ids, $sort_by = 'created', $sort_direction = 'DESC', $limit_from = false, $limit_per_page = false)
    {
    
    	/**
		 * input filter
		 */

		// sorting
		if (!in_array($sort_by, array('title', 'created', 'modified', 'priority', 'share_counter'))) $sort_by = 'created';
		if (!in_array($sort_direction, array('DESC', 'ASC'))) $sort_direction = 'DESC';
		$order_by = " ORDER BY $sort_by $sort_direction";
		
		// limit
		if  (!is_numeric($limit_from)) $limit_from = false;
		if (!is_numeric($limit_per_page)) $limit_per_page = false;
		
		// allow to use limit_per_page without providing limit_from
		if (is_numeric($limit_per_page) && $limit_from === false) $limit_from = 0;
		
		if (is_numeric($limit_from) && is_numeric($limit_per_page)) {
			$limit = " LIMIT $limit_per_page OFFSET $limit_from";
		} else {
			$limit = '';
		}
		
    	/**
    	 * initialise
    	 */
    	 
   		require_once('models/common/common_node.php');
 		require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');
 		require_once('models/ecommerce/ecommerce_recipe_image.php');
 		require_once('models/ecommerce/ecommerce_recipe_review.php');

    	$Node = new common_node();
		$Image = new ecommerce_recipe_image();
		$Taxonomy = new ecommerce_recipe_taxonomy();
		$Review = new ecommerce_recipe_review();
		
		/**
		 * recipes list
		 */
		 
		$recipes = array();

		if (is_array($taxonomy_ids) && count($taxonomy_ids) > 0) {
		
			$taxonomy = $Taxonomy->listing("taxonomy_tree_id IN (" . implode(",", $taxonomy_ids) . ")");

			$recipe_ids = array();
			foreach ($taxonomy as $category) {
				$recipe_ids[] = $category['node_id'];
			}

			$where = "ecommerce_recipe.id IN (" . implode(",", $recipe_ids) . ") AND ecommerce_recipe.publish = 1";
			
			$sql = "SELECT ecommerce_recipe.*, common_node.share_counter
				FROM ecommerce_recipe
				INNER JOIN common_node ON (common_node.node_group = 'page' 
					AND common_node.node_controller = 'recipe'
					AND common_node.content = ecommerce_recipe.id::varchar
					AND common_node.publish = 1)
				WHERE $where
				$order_by
				$limit";
		
			$recipes = $this->executeSql($sql);
			
			// return empty array if nothing is found
			if (!is_array($recipes)) return array();
			
			$recipe_pages = $Node->listing("node_group = 'page' AND node_controller = 'recipe' AND content ~ '[0-9]+' AND publish = 1");

			foreach ($recipe_pages as $recipe_page)
				foreach ($recipes as &$recipe) {
					if ($recipe_page['content'] == $recipe['id']) {
						// asign page
						$recipe['page'] = $recipe_page;

						// load images
						$image_list = $Image->listFiles($recipe['id'] , $priority = "priority DESC, id ASC", false);
						$recipe['image']  = $image_list[0];
						
						// load review
						$recipe['review'] = $Review->getRating($recipe['id']);
						
					}
				}

		}
		
 		return $recipes;

    }
    
    /**
	 * get taxonomy relation
	 */
	 
	function getTaxonomyForRecipe($recipe_id) {
	
		if (!is_numeric($recipe_id)) return false;
		
		require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');
		$Taxonomy = new ecommerce_recipe_taxonomy();
		
		$relations = $Taxonomy->getRelationsToRecipe($recipe_id);
		
		return $relations;
	}
	
	/**
	 * getRelatedTaxonomy
	 */
	 
	public function getRelatedTaxonomy($recipe_id) {
		
		if (!is_numeric($recipe_id)) return false;
		
		require_once('models/common/common_taxonomy.php');
		$Taxonomy = new common_taxonomy();
		$related_taxonomy = $Taxonomy->getRelatedTaxonomy($recipe_id, 'ecommerce_recipe_taxonomy');
		
		return $related_taxonomy;
	}

}