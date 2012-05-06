<?php
/** 
 * Copyright (c) 2007-2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * product export for http://www.zbozi.cz
 * http://napoveda.seznam.cz/cz/specifikace-xml.html
 */

class Onxshop_Controller_Export_Xml_Zbozi extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		header('Content-Type: text/xml; charset=UTF-8');
		
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		
		require_once('models/ecommerce/ecommerce_product_image.php');
		$Image = new ecommerce_product_image();
		
		$products = $Product->getProductList();
		//print_r($products);exit;
		
		$this->tpl->assign('ITEM_TYPE', 'new');//new or bazaar
		$this->tpl->assign('DUES', 0);//poplatky mimo postovneho
		$this->tpl->assign('DELIVERY_DATE', 1);//doba expedice
		$this->tpl->assign('TOLLFREE', 0);
		
		foreach ($products as $p) {
		
			//get product detail URL
			$current = $Product->findProductInNode($p['id']);
			$product_node_data = $current[0];
			$page_id = $product_node_data['id'];
			$p['url'] = "http://{$_SERVER['HTTP_HOST']}" . $Node->getSeoURL($page_id);
			
			//description
			$p['description'] = html_entity_decode(strip_tags($p['description']), ENT_QUOTES, 'UTF-8');
			
			//image
			$images = $Image->listing("role = 'main' AND node_id=".$p['id'], "priority DESC, id ASC", '0,1');
			$this->tpl->assign('IMAGE_PRODUCT', "http://{$_SERVER['HTTP_HOST']}/image/{$images[0]['src']}");
		
			//assign to template
			$this->tpl->assign('PRODUCT', $p);
			
			//variety list
			if (is_array($p['variety'])) {
				foreach ($p['variety'] as $v) {
					//$v['description'] = html_entity_decode(strip_tags($v['description']));
					$this->tpl->assign('VARIETY', $v);
					$this->tpl->assign('PRICE', $v['price']['value']);
					if ($v['publish'] == 1) $this->tpl->parse("content.item_product.item_variety");
				}
			}
			
			if ($p['publish'] == 1) $this->tpl->parse("content.item_product");
		}

		return true;
	}
}
