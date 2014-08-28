<?php
/** 
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_2/recipe_search.php');

class Onxshop_Controller_Api_v1_3_Recipe_Search extends Onxshop_Controller_Api_v1_2_Recipe_Search {

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
		$item['images'] = array("http://" . $_SERVER['HTTP_HOST'] . "/image/" . $original_item['image']['src']);
		$item['thumbnails'] = array("http://" . $_SERVER['HTTP_HOST'] . "/thumbnail/" . self::$thumbnail_size . '/'. $original_item['image']['src']);
		$item['video'] = $original_item['video_url'];
		$item['ready_time'] = $original_item['preparation_time'] + $original_item['cooking_time'];
		$item['meal_types'] = array();
		$item['categories'] = array(); // TODO
		$item['url'] = "http://" . $_SERVER['HTTP_HOST'] . "/recipe/{$original_item['id']}";
		
		return $item;
		
	}

}
