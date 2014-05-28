<?php
/** 
 * Copyright (c) 2012-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api.php');

class Onxshop_Controller_Api_v1_0_Recipe_Search extends Onxshop_Controller_Api {

	/**
	 * get data
	 */
	
	public function getData() {
		
		/**
		 * input
		 */
		 
		if (is_numeric($this->GET['category_id'])) $category_id = $this->GET['category_id'];
		else $category_id = false;
		
		if (is_numeric($this->GET['meal_type_id'])) $meal_type_id = $this->GET['meal_type_id']; //course, 903 Dessert
		else $meal_type_id = false;
		
		if ($this->GET['product_id']) $product_id = $this->GET['product_id'];
		else $product_id = false;
		
		if ($this->GET['keyword']) $keyword = $this->GET['keyword'];
		else $keyword = false;
		
		if (is_numeric($this->GET['ready_time'])) $ready_time = $this->GET['ready_time'];
		else $ready_time = false;
		
		/**
		 * initialize
		 */
		 
		require_once('models/ecommerce/ecommerce_recipe.php');
		$Recipe = new ecommerce_recipe();
		
		/**
		 * get recipe list
		 */
		
		$taxonomy_ids = array();
		if (is_numeric($category_id)) $taxonomy_ids[] = $category_id;
		if (is_numeric($meal_type_id)) $taxonomy_ids[] = $meal_type_id;
		
		$recipe_list = $Recipe->getRecipeListForTaxonomy($taxonomy_ids);
		
		/**
		 * get extra info
		 */
		
		$data = array();
		
		foreach($recipe_list as $item ) {
			
			$data[] = $this->formatItem($item);
			
		}
		
		/**
		 * return array
		 */
		
		return $data;
		
	}
	
	/**
	 * formatItem
	 */
	 
	static function formatItem($original_item) {
		
		if (!is_array($original_item)) return false;
		
		$item = array();
		$item['id'] = $original_item['id'];
		$item['title'] = $original_item['title'];
		$item['description'] = strip_tags($original_item['description']);
		$item['image_thumbnail'] = "http://" . $_SERVER['HTTP_HOST'] . "/image/" . $original_item['image']['src'];
		$item['ready_time'] = $original_item['preparation_time'] + $original_item['cooking_time'];
		$item['meal_types'] = array();
		$item['categories'] = array(); // TODO
		$item['url'] = "http://" . $_SERVER['HTTP_HOST'] . "/recipe/{$original_item['id']}";
		
		return $item;
		
	}
	
}
