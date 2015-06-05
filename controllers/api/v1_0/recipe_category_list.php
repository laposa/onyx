<?php
/** 
 * Copyright (c) 2013-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api.php');

class Onxshop_Controller_Api_v1_0_Recipe_Category_List extends Onxshop_Controller_Api {

	/**
	 * get data
	 */
	
	public function getData() {
		
		/**
		 * initialize
		 */
		 
		require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');
		$RecipeTaxonomy = new ecommerce_recipe_taxonomy();
		
		/**
		 * get recipe categories
		 */
		
		$data_original = $RecipeTaxonomy->getUsedTaxonomyLabels();
		$data = array();
		
		/**
		 * format
		 */
		 
		foreach ($data_original as $item_original) {
			
			if ($item = $this->formatItem($item_original)) $data[] = $item;;
			
		}
		
		return $data;
		
	}
	
	/**
	 * formatItem
	 */
	
	public function formatItem($item_original) {
		
		if (!is_array($item_original)) return false;
		
		if ($item_original['publish'] == 1) {
			
			$item = array();
			$item['id'] = $item_original['id'];
			$item['title'] = $item_original['title'];
			$item['description'] = $item_original['description'];
			$item['image_thumbnail'] = $this->getImageThumbnailSrc($item_original['id']);
			$item['priority'] = $item_original['priority'];
			$item['usage_count'] = 999;
			
			return $item;
		} else {
			
			return false;
			
		}
	}
	
	/**
	 * getImageThumbnailSrc
	 *
	 * @return URL string
	 */
	 
	public function getImageThumbnailSrc($label_id) {
		
		if (!is_numeric($label_id)) return false;
		
		require_once('models/common/common_taxonomy.php');
		$Taxonomy = new common_taxonomy();
		
		$images = $Taxonomy->getLabelImages($label_id);
		
		if (is_array($images) && count($images) > 0) return "//{$_SERVER['HTTP_HOST']}/image/" . $images[0]['src'];
		else return '';
	}
	
}
