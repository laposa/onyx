<?php
/**
 * class ecommerce_recipe
 *
 * Copyright (c) 2013-2015 Laposa Ltd (http://laposa.co.uk)
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
	 *
	 * we also user ready_time = preparation_time + cooking_time
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
		
		if (!is_numeric($conf['taxonomy_tree_id'])) $conf['taxonomy_tree_id'] = 4; // experimental
		
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
	 * prepareRecipeFilterSql
	 */
	private function prepareRecipeFilterSql($keywords, $ready_time, $taxonomy_id, $product_variety_sku, $publish)
	{
		if ($publish) $where = 'ecommerce_recipe.publish = 1 ';
		else $where = '1 = 1 ';

		// keywords SQL
		$keywords = str_replace(",", " ", $keywords);
		if (strlen(trim($keywords)) > 0) {

			$keywords_array = explode(" ", $keywords);

			foreach ($keywords_array as $keyword) {

				$keyword = trim(pg_escape_string($keyword));

	    		if (strlen($keyword) > 0) {
					// title, description, instructions
	    			$where .= " AND (ecommerce_recipe.title ILIKE '%{$keyword}%' " .
	    				"OR ecommerce_recipe.description ILIKE '%{$keyword}%' " .
	    				"OR ecommerce_recipe.instructions ILIKE '%{$keyword}%' ";

					// ingredients
					$product_ids = "SELECT id FROM ecommerce_product WHERE name ILIKE '%{$keyword}%'";
					$product_variety_ids = "SELECT id FROM ecommerce_product_variety WHERE product_id IN ($product_ids)";
					$recipe_ids = "SELECT recipe_id FROM ecommerce_recipe_ingredients WHERE product_variety_id IN ($product_variety_ids)";
	    			$where .= " OR ecommerce_recipe.id IN ($recipe_ids))";
				}
			}
		}
		
		// product SKU
		if (strlen(trim($product_variety_sku)) > 0) {
			$product_variety_sku_array = explode(",", $product_variety_sku);
			$product_variety_sku = array();
			foreach ($product_variety_sku_array as $sku) {
				$product_variety_sku[] = "'" . pg_escape_string($sku) . "'";
			}
			$product_variety_sku = implode(",", $product_variety_sku);
			if (count($product_variety_sku) > 0) {
				$product_variety_ids = "SELECT id FROM ecommerce_product_variety WHERE ecommerce_product_variety.sku IN ($product_variety_sku)";
				$recipe_ids = "SELECT recipe_id FROM ecommerce_recipe_ingredients WHERE product_variety_id = ALL ($product_variety_ids)";
				$where .= " AND ecommerce_recipe.id IN ($recipe_ids)";
			}
		}

    	//taxonomy
    	if (is_numeric($taxonomy_id) && $taxonomy_id > 0) $where .= " AND ecommerce_recipe.id IN (SELECT node_id FROM ecommerce_recipe_taxonomy WHERE taxonomy_tree_id = $taxonomy_id)";

    	//time
    	if (is_numeric($ready_time) && $ready_time > 0) $where .= " AND (ecommerce_recipe.preparation_time + ecommerce_recipe.cooking_time) <= $ready_time";

    	return $where;
	}

	/**
	 * Get filtered recipe list
	 * 
	 * @param  string $keywords            Search keywords (looking though title, description, instructions and ingredients)
	 * @param  string $ready_time          Maximum ready time (cooking + preparation time)
	 * @param  string $taxonomy_id         Recipe category
	 * @param  string $product_variety_sku Filter by product SKU(s)
	 * @param  string $limit_per_page      Page Limit
	 * @param  string $limit_from          Page Limit
	 * @param  string $order_by            Ordering
	 * @param  string $order_dir           Ordering
	 * @param  boolean $publish            Publish filter
	 * @return array
	 */
	function getFilteredRecipeList($keywords = false, $ready_time = false, $taxonomy_id = false, $product_variety_sku = false, $limit_per_page = false, $limit_from = false, $order_by = false, $order_dir = false, $publish = 1)
	{
		$where = $this->prepareRecipeFilterSql($keywords, $ready_time, $taxonomy_id, $product_variety_sku, $publish);

		// limits
		if (!is_numeric($limit_from)) $limit_from = 0;
		if (!is_numeric($limit_per_page)) $limit_per_page = 25;

		// order
		if (in_array($order_by, array('title', 'created', 'modified', 'priority', 'share_counter'))) {
			$order = "$order_by";
			if ($order_dir == 'DESC') $order .= " DESC";
			else $order .= " ASC";
		} else {
			$order = "priority DESC, ecommerce_recipe.id DESC";
		}

		// sql
		$sql = "SELECT * FROM ecommerce_recipe WHERE " . $where .
			" ORDER BY $order" .
			" LIMIT $limit_per_page OFFSET $limit_from";

		// list
		$records = $this->executeSql($sql);

		if (is_array($records)) {

			require_once('models/ecommerce/ecommerce_recipe_image.php');
			$Image = new ecommerce_recipe_image();

			require_once('models/common/common_node.php');
			$Node = new common_node();

			$recipe_pages = $Node->listing("node_group = 'page' AND node_controller = 'recipe' AND content ~ '[0-9]+' AND publish = 1");

			foreach ($records as $i => $item) {
				$images = $Image->listFiles($item['id']);
				if (count($images) > 0) {
					$records[$i]['image']['src'] = $images[0]['src'];
					$records[$i]['image']['title'] = $images[0]['title'];
				}
				foreach ($recipe_pages as $recipe_page) {
					if ($recipe_page['content'] == $item['id']) {
						$records[$i]['page'] = $recipe_page;
					}
				}
			}

			return $records;
		}

		return array();

	}

	/**
	 * getFilteredRecipeCount
	 */

	function getFilteredRecipeCount($keywords = false, $ready_time = false, $taxonomy_id = false, $product_variety_sku = false, $publish = 1)
	{
		$where = $this->prepareRecipeFilterSql($keywords, $ready_time, $taxonomy_id, $product_variety_sku, $publish);
		return $this->count($where);
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

			$where = "AND ecommerce_recipe.id IN (" . implode(",", $recipe_ids) . ")";

		} else {

			$where = "";
		}
			
		$sql = "SELECT ecommerce_recipe.*, common_node.share_counter
			FROM ecommerce_recipe
			INNER JOIN common_node ON (common_node.node_group = 'page' 
				AND common_node.node_controller = 'recipe'
				AND common_node.content = ecommerce_recipe.id::varchar
				AND common_node.publish = 1)
			WHERE ecommerce_recipe.publish = 1 $where
			$order_by
			$limit";
	
		$recipes = $this->executeSql($sql);
		
		// return empty array if nothing is found
		if (!is_array($recipes)) return array();
		
		$recipe_pages = $Node->listing("node_group = 'page' AND node_controller = 'recipe' AND content ~ '[0-9]+' AND publish = 1");

		foreach ($recipe_pages as $recipe_page) {
			foreach ($recipes as &$recipe) {
				if ($recipe_page['content'] == $recipe['id']) {

					// assign page
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
     * getRecipeCountForTaxonomy
     */

    function getRecipeCountForTaxonomy($taxonomy_ids) {

 		require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');
		$Taxonomy = new ecommerce_recipe_taxonomy();

		if (is_array($taxonomy_ids) && count($taxonomy_ids) > 0) {
		
			$taxonomy = $Taxonomy->listing("taxonomy_tree_id IN (" . implode(",", $taxonomy_ids) . ")");

			$recipe_ids = array();
			foreach ($taxonomy as $category) {
				$recipe_ids[] = $category['node_id'];
			}

			$where = "AND ecommerce_recipe.id IN (" . implode(",", $recipe_ids) . ")";

		} else {

			$where = "";
		}
		
		$sql = "SELECT count(ecommerce_recipe.id) AS count
			FROM ecommerce_recipe
			INNER JOIN common_node ON (common_node.node_group = 'page' 
				AND common_node.node_controller = 'recipe'
				AND common_node.content = ecommerce_recipe.id::varchar
				AND common_node.publish = 1)
			WHERE ecommerce_recipe.publish = 1 $where";
	
		$result = $this->executeSql($sql);
		return (int) $result[0]['count'];

    }

    
    /**
	 * get taxonomy relation
	 */
	 
	static function getTaxonomyForRecipe($recipe_id) {
	
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
	
	/**
	 * getRecipeTaxonomy
	 * TODO: this should re-used or merged with /api/v1.3/recipe_category_list
	 */
	 
	public function getRecipeTaxonomy() {
		
		// get categories from taxonomy
		require_once('models/common/common_taxonomy.php');
		$Taxonomy = new common_taxonomy;
		
		$recipe_categories = $Taxonomy->getChildren($this->conf['taxonomy_tree_id']);
		
		return $recipe_categories;
		
	}

}
