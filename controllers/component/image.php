<?php
/** 
 * Copyright (c) 2006-2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Image extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * getImageList
		 */
		 
		$image_list = $this->getImageList();
		
		/**
		 * assignAndParse (main images)
		 */
		 
		$this->assignAndParse($image_list);


		return true;
	}
	
	/**
	 * getImageList
	 *
	 * @return array
	 * image list
	 */
	
	public function getImageList() {

		/**
		 * setting input variables
		 */
	
		if ($this->GET['relation']) $relation = preg_replace('/[^a-zA-Z_-]/', '', $this->GET['relation']);
		else $relation = '';
		
		if ($this->GET['role']) $role = preg_replace('/[^a-zA-Z_-]/', '', $this->GET['role']);
		else $role = false;

		if (is_numeric($this->GET['node_id'])) {
		
			$node_id = $this->GET['node_id'];
		
		} else {
		
			msg('image: node_id is not numeric', 'error');
			return false;
		
		}

		if ($this->GET['limit']) $limit = $this->GET['limit'];
		else $limit = "";
		
		
		/**
		 * creating image object
		 */
		 
		$this->Image = $this->createImageObject($relation);
		
		/**
		 * get list of images
		 */
		
		$image_list = $this->Image->listFiles($node_id , $role, "priority DESC, id ASC", $limit);
				
		/**
		 * save image count in registry for use in image_gallery
		 */
		
		//TODO
		
		/**
		 * return list of images found
		 */

		return $image_list;
	}
	
	/**
	 * assign and parse image list to template
	 *
	 * @param array $image_list
	 *
	 * @return bool 
	 */
	 
	public function assignAndParse($image_list) {
		
		$img_path = $this->getImagePath();
		
		/**
		 * set full width based on restrictions in Image->conf
		 */
		 
		if ($this->Image->conf['width_max'] > 0 && is_numeric($this->Image->conf['width_max'])) $this->tpl->assign('FULL_SIZE_IMAGE_WIDTH_PATH', "/thumbnail/{$this->Image->conf['width_max']}/");
		else $this->tpl->assign('FULL_SIZE_IMAGE_WIDTH_PATH', "/image/");
		
		/**
		 * save first image in template variable as helper for a placeholder
		 */
		 
		$this->tpl->assign('FIRST_IMAGE', $image_list[0]);
		
		/**
		 * assign & parse each item to template
		 */
		 
		foreach ($image_list as $k=>$item) {
			
			if ($k == 0) $this->tpl->assign('FIRST_LAST', 'first');
			else if ($k == ($image_count - 1)) $this->tpl->assign('FIRST_LAST', 'last');
			else $this->tpl->assign('FIRST_LAST', '');
				
			$item['path'] = $image_list[$k]['path'] = $img_path;
			
			$this->tpl->assign('INDEX', $k);
			
			$this->assignAndParseItem($item);
			
		}
		
		return true;
	}
	
	/**
	 * assignAndParseItem
	 */
	
	public function assignAndParseItem($item) {
		
		$this->tpl->assign('ITEM', $item);
		$this->tpl->parse('content.item');
			
	}
	
	/**
	 * Create image object
	 * 
	 * @param string
	 * relation (product, product_variety, taxonomy, recipe, store, node)
	 *
	 * @return object
	 * common_image
	 */
	
	function createImageObject($relation) {
	
		switch ($relation) {
			case 'product':
				require_once('models/ecommerce/ecommerce_product_image.php');
				$Image = new ecommerce_product_image();
			break;
			case 'product_variety':
				require_once('models/ecommerce/ecommerce_product_variety_image.php');
				$Image = new ecommerce_product_variety_image();
			break;
			case 'taxonomy':
				require_once('models/common/common_taxonomy_label_image.php');
				$Image = new common_taxonomy_label_image();
			break;
			case 'recipe':
				require_once('models/ecommerce/ecommerce_recipe_image.php');
				$Image = new ecommerce_recipe_image();
			break;
			case 'store':
				require_once('models/ecommerce/ecommerce_store_image.php');
				$Image = new ecommerce_store_image();
			break;
			case 'node':
			default:
				require_once('models/common/common_image.php');
				$Image = new common_image();
		}

		return $Image;
	}
	
	/**
	 * get image path
	 *
	 * @return string
	 * image path based on width and height requested via HTTP GET
	 */
	 
	public function getImagePath() {
		
		/**
		 * check requested width - for generating IMAGE_PATH
		 */
		 
		if (is_numeric($this->GET['width'])) $width = $this->GET['width'];
		else $width = 100;
		
		/**
		 * check requested height
		 */
		 
		if (is_numeric($this->GET['height']) && $this->GET['height'] > 0) $height = $this->GET['height'];
		else $height = 0;
		
		/**
		 * set path
		 */
		 
		if ($width == 0) $image_path = "/image/";
		else if ($height > 0) $image_path = "/thumbnail/{$width}x{$height}/";
		else $image_path = "/thumbnail/{$width}/";
		
		/**
		 * other resize options - generating IMAGE_RESIZE_OPTIONS
		 */
		
		$image_resize_options = array();
		
		if ($this->GET['method']) $image_resize_options['method'] = $this->GET['method'];
		if ($this->GET['gravity']) $image_resize_options['gravity'] = $this->GET['gravity'];
		if ($this->GET['fill']) $image_resize_options['fill'] = $this->GET['fill'];
		
		if (count($image_resize_options) > 0) $this->tpl->assign('IMAGE_RESIZE_OPTIONS', '?'.http_build_query($image_resize_options));
		
		
		/**
		 * return path string
		 */
		 
		return $image_path;
	}
}
