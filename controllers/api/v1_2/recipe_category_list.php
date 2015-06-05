<?php
/** 
 * Copyright (c) 2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_1/recipe_category_list.php');

class Onxshop_Controller_Api_v1_2_Recipe_Category_List extends Onxshop_Controller_Api_v1_1_Recipe_Category_List {
	
	/**
	 * formatItem
	 */
	
	public function formatItem($item_original) {
		
		if (!is_array($item_original)) return false;

		$item = array();
		$item['id'] = $item_original['id'];
		$item['parent'] = 0; //TODO
		$item['title'] = $item_original['title'];
		$item['description'] = $item_original['description'];
		$item['priority'] = $item_original['priority'];
		$item['publish'] = $item_original['publish'];
		$item['image_thumbnail'] = $this->getImageThumbnailSrc($item_original['id']);
					
		return $item;
		
	}
	
}
