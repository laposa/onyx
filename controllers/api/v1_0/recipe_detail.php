<?php
/** 
 * Copyright (c) 2013-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api.php');

class Onxshop_Controller_Api_v1_0_Recipe_Detail extends Onxshop_Controller_Api {

	/**
	 * get data
	 */
	
	public function getData() {
		
		/**
		 * input
		 */
		 
		if (is_numeric($this->GET['recipe_id'])) $recipe_id = $this->GET['recipe_id'];
		else {
			//msg("missing recipe_id", 'error');
			$data = array();
			$data['message'] = "missing recipe_id";
			$data['status'] = 400;
			
			return $data;
			
		}
		
		/**
		 * initialize
		 */
		 
		require_once('models/ecommerce/ecommerce_recipe.php');
		$Recipe = new ecommerce_recipe();
		
		/**
		 * get recipe page posts
		 */
		
		$list = $Recipe->getFilteredRecipeList(false, $recipe_id);
		
		$data = array();
		
		foreach($list[0] as $item ) {
			
			if ($item['publish'] == 1) {
				
				$item = $this->formatItem($item);
				
				$data[] = $item;
			}
			
		}
		
		return $data;
		
	}
	
	/**
	 * formatItem
	 */
	 
	static function formatItem($original_item) {
		
		if (!is_array($original_item)) return false;
		
		$item = array();
		
		if ($_SERVER['SSL_PROTOCOL'] || $_SERVER['HTTPS']) $protocol = 'https';
		else $protocol = 'http';
			
		$item['id'] = (int)$original_item['id'];
		$item['title'] = $original_item['title'];
		$item['description'] = preg_replace("/[\r\n\t]/", " ", strip_tags($original_item['description']));
		$item['instructions'] = preg_replace("/[\r\n\t]/", " ", $original_item['instructions']);
		$item['url'] = "$protocol://{$_SERVER['HTTP_HOST']}/recipe/" . $original_item['id'];
		$item['priority'] = (int)$original_item['priority'];
		$item['created'] = $original_item['created'];
		$item['modified'] = $original_item['modified'];
		
		$item['ingredients'] = self::getIngredients($item['id']);
		$item['categories'] = self::getCategories($item['id']);
		$item['images'] = array("$protocol://{$_SERVER['HTTP_HOST']}/image/" . $original_item['image_src']);
		$item['video'] = (int)self::getVideoIdFromUrl($original_item['video_url']);
		$item['comments'] = array();
		$item['rating'] = array();
		$item['serving_people'] = (int)$original_item['serving_people'];
		$item['preparation_time'] = (int)$original_item['preparation_time'];
		$item['cook_time'] = (int)$original_item['cooking_time'];
		$item['recommended_wines'] = array();//$this->getRecommendedWines($post->ID);
		$item['related_offers'] = array();//$this->getSpecialOffersForRecipe($post->ID);
		$item['meal_types'] = $item['categories'];//$this->getMealTypesForRecipe($post->ID);
		
		return $item;
		
	}
	
	/**
	 * getIngredients
	 */
	
	static function getIngredients($recipe_id) {
		
		if (!is_numeric($recipe_id)) return false;
		
		require_once('models/ecommerce/ecommerce_recipe_ingredients.php');
		$Ingredients = new ecommerce_recipe_ingredients();
		
		return $Ingredients->getIngredientsForRecipeOptimised($recipe_id);
		
	}
	
	/**
	 * getIngredients
	 */
	
	static function getCategories($recipe_id) {
		
		return array();
		
	}
	
	/**
	 * getVideoIdFromUrl
	 */
	 
	static function getVideoIdFromUrl($video_url) {
		
		return 0;
		
	}
}
