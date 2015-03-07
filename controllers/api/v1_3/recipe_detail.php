<?php
/** 
 * Copyright (c) 2014-2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_2/recipe_detail.php');

class Onxshop_Controller_Api_v1_3_Recipe_Detail extends Onxshop_Controller_Api_v1_2_Recipe_Detail {

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
		$item['thumbnails'] = array("$protocol://{$_SERVER['HTTP_HOST']}/thumbnail/" . self::$thumbnail_size . '/' . $original_item['image_src']);
		$item['video'] = $original_item['video_url'];
		$item['comments'] = array();
		$item['rating'] = array();
		$item['serving_people'] = (int)$original_item['serving_people'];
		$item['preparation_time'] = (int)$original_item['preparation_time'];
		$item['cook_time'] = (int)$original_item['cooking_time'];
		$item['recommended_wines'] = array();//$this->getRecommendedWines($post->ID);
		$item['related_offers'] = array();//$this->getSpecialOffersForRecipe($post->ID);
		$item['meal_types'] = $item['ingredients'];//$this->getMealTypesForRecipe($post->ID);
		
		return $item;
		
	}

}
