<?php
/** 
 * Copyright (c) 2012-2015 Laposa Ltd (http://laposa.co.uk)
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
		$this->Recipe = new ecommerce_recipe();
		
		/**
		 * get recipe list
		 */
		
		$ingredients = $keyword;
		$recipe_list_x = $this->Recipe->searchRecipes($keyword, $ingredients, $ready_time, $category_id, $product_id);
		$recipe_list = $recipe_list_x[0]; // array($records, $count);
		
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
	 
	public function formatItem($original_item) {
		
		if (!is_array($original_item)) return false;
		
		if ($_SERVER['SSL_PROTOCOL'] || $_SERVER['HTTPS']) $protocol = 'https';
		else $protocol = 'http';
		
		$item = array();
		$item['id'] = $original_item['id'];
		$item['title'] = $original_item['title'];
		$item['description'] = strip_tags($original_item['description']);
		$item['image_thumbnail'] = "$protocol://" . $_SERVER['HTTP_HOST'] . "/image/" . $original_item['image']['src'];
		$item['ready_time'] = $original_item['preparation_time'] + $original_item['cooking_time'];
		$item['meal_types'] = $this->getMealTypes($original_item);
		$item['categories'] = $this->getCategories($original_item);
		$item['url'] = "$protocol://" . $_SERVER['HTTP_HOST'] . "/recipe/{$original_item['id']}";
		
		return $item;
		
	}
	
	/**
	 * getMealTypes
	 */
	 
	public function getMealTypes($original_item) {
		
		$categories = $this->Recipe->getRelatedTaxonomy($original_item['id']);
		
		$formatted = array();
		
		foreach ($categories as $k=>$item) {
		
			$formatted[$k]['id'] = $item['id'];
			$formatted[$k]['title'] = $item['title'];
		
		}
		
		return $formatted;
			
	}
	
	/**
	 * getMealTypes
	 */
	 
	public function getCategories($original_item) {
		
		$categories = $this->Recipe->getRelatedTaxonomy($original_item['id']);
		
		$formatted = array();
		
		foreach ($categories as $k=>$item) {
		
			$formatted[$k]['id'] = $item['id'];
			$formatted[$k]['title'] = $item['title'];
			$formatted[$k]['priority'] = $item['priority'];
			$formatted[$k]['usage_count'] = 1;
		
		}
		
		return $formatted;
		
		return array();
			
	}
	
}
