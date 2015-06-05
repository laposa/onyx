<?php
/** 
 * Copyright (c) 2014-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_2/recipe_search.php');

class Onxshop_Controller_Api_v1_3_Recipe_Search extends Onxshop_Controller_Api_v1_2_Recipe_Search {

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
		$item['images'] = array("$protocol://" . $_SERVER['HTTP_HOST'] . "/image/" . $original_item['image']['src']);
		$item['thumbnails'] = array("$protocol://" . $_SERVER['HTTP_HOST'] . "/thumbnail/" . self::$thumbnail_size . '/'. $original_item['image']['src']);
		$item['video'] = $original_item['video_url'];
		$item['ready_time'] = $original_item['preparation_time'] + $original_item['cooking_time'];
		$item['meal_types'] = $this->getMealTypes($original_item); // TODO remove in v1.4
		$item['categories'] = $this->getCategories($original_item);
		$item['url'] = "$protocol://" . $_SERVER['HTTP_HOST'] . "/recipe/{$original_item['id']}";
		
		return $item;
		
	}
	
	/**
	 * getMealTypes
	 */
	 
	public function getMealTypes($original_item) {
		
		return array(); //return empty array, TODO: this will be removed in v1.4
			
	}
	
	/**
	 * getMealTypes
	 */
	 
	public function getCategories($original_item) {
		
		return array(); // return empty array from historic reasons, TODO: implement correctly in v1.4
			
	}

}
