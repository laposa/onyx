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
			
			$item_formated = array();
			$item_formated['id'] = $item['id'];
			$item_formated['title'] = $item['title'];
			$item_formated['description'] = strip_tags($item['description']);
			$item_formated['image_thumbnail'] = "http://" . $_SERVER['HTTP_HOST'] . "/image/" . $item['image']['src'];
			$item_formated['ready_time'] = $item['preparation_time'] + $item['cooking_time'];
			$item_formated['meal_types'] = array();
			$item_formated['categories'] = array(); // TODO
			$item_formated['url'] = "http://" . $_SERVER['HTTP_HOST'] . "/recipe/{$item['id']}";
			
			$data[] = $item_formated;
			
		}
		
		/**
		 * return array
		 */
		
		return $data;
		
	}
	
}
