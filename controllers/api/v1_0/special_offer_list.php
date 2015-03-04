<?php
/** 
 * Copyright (c) 2013-2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api.php');

class Onxshop_Controller_Api_v1_0_Special_Offer_List extends Onxshop_Controller_Api {

	/**
	 * get data
	 */
	
	public function getData() {

		$data = '';
		
		/**
		 * initialize
		 */
		 
		require_once('models/ecommerce/ecommerce_offer.php');
		$Offer = new ecommerce_offer();
		
		/**
		 * get special offer list
		 */
		
		$records = $Offer->getActiveOffers();
		
		$data = array();
		
		foreach($records as $record) {

			$item = $this->formatItem($record);
			//API 1.0 is showing expiry date without time and as day when the offer is taken down
			$item['expiry_date'] = date('Y-m-d', strtotime($item['expiry_date']) + 86400);
			
			$data[] = $item;
			
		}
			
		return $data;
		
	}
	
	/**
	 * formatItem
	 */
	 
	public function formatItem($original_item) {
		
		if (!is_array($original_item)) return false;
		$original_item['price_formatted'] = $this->formatPrice($original_item['price'], $original_item['currency_code']);
		
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		$product_detail = $Product->getProductDetail($original_item['product_id']);
		//print_r($product_detail); exit;
		
		if ($_SERVER['SSL_PROTOCOL'] || $_SERVER['HTTPS']) $protocol = 'https';
		else $protocol = 'http';
		
		$item['id'] = $original_item['offer_id'];
		$item['title'] = $product_detail['name'];
		$item['content'] = strip_tags($product_detail['description']);
		$item['url'] = "$protocol://{$_SERVER['HTTP_HOST']}/product/{$original_item['product_id']}";
		$item['priority'] = $product_detail['priority'];
		$item['created'] = $product_detail['modified'];
		$item['modified'] = $product_detail['modified'];
		$item['images'] = array("$protocol://{$_SERVER['HTTP_HOST']}/thumbnail/180x180/" . $Product->getProductMainImageSrc($original_item['product_id']));
		$item['rondel'] = $this->getRoundelText($original_item);
		$item['rondel_image_url'] = $this->getRoundelImageSource($original_item);
		$item['price'] = money_format('%n', $original_item['price']);
		$item['expiry_date'] = $original_item['group_schedule_end'];
		$item['taxonomy'] = $this->getTaxonomy($original_item['product_id'], $Product);
		$item['product_id'] = $product_detail['variety'][0]['sku'];//TODO this is showing only first ones
		//special offer group
		$item['group_id'] = $original_item['group_id'];
		$item['group_title'] = $original_item['group_title'];
		
		return $item;	
	}
	
	/**
	 * formatPrice
	 */
	 
	function formatPrice($value, $currency_code)
	{
		require_once('controllers/component/ecommerce/roundel_css.php');
		return Onxshop_Controller_Component_Ecommerce_Roundel_CSS::formatPrice($value, $currency_code);
	}
	
	/**
	 * getRoundelText
	 */
	 
	public function getRoundelText($offer)
	{
		require_once('controllers/component/ecommerce/roundel_css.php');
		return Onxshop_Controller_Component_Ecommerce_Roundel_CSS::getRoundelText($offer);
	}
	
	/**
	 * getRoundelImageSource
	 */
	 
	public function getRoundelImageSource($offer)
	{
		require_once('controllers/component/ecommerce/roundel_css.php');
		$image_src = Onxshop_Controller_Component_Ecommerce_Roundel_CSS::getRoundelImageSource($offer);
		
		if ($_SERVER['SSL_PROTOCOL'] || $_SERVER['HTTPS']) $protocol = 'https';
		else $protocol = 'http';
		
		$image_src = "$protocol://{$_SERVER['HTTP_HOST']}/$image_src";
		
		return $image_src;
	}
	
	/**
	 * getTaxonomy
	 */
	 
	public function getTaxonomy($product_id, $Product) {
	
		$taxonomy_list = $Product->getRelatedTaxonomy($product_id);
	
		$formatted = array();
		
		foreach ($taxonomy_list as $item) {
			
			$formatted_item = array();
			$formatted_item['term_id'] = $item['id'];
			$formatted_item['name'] = $item['title'];
			$formatted_item['slug'] = '';
			$formatted_item['term_group'] = 0;
			$formatted_item['term_order'] = 0;
			$formatted_item['usage_count'] = 1;
			$formatted_item['description'] = $item['description'];
			
			$formatted[] = $formatted_item;
		}
		
		return $formatted;
	}
}
