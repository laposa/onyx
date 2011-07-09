<?php
/** 
 * Copyright (c) 2007-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * product export for http://www.zbozi.cz
 */

class Onxshop_Controller_Export_Xml_Zbozi extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		//SELECT * FROM ecommerce_product p, ecommerce_product_variety v, ecommerce_price price WHERE p.id = v.product_id AND price.product_variety_id = v.id
		header('Content-Type: text/xml; charset=UTF-8');
		
		// flash in IE with SSL dont like Cache-Control: no-cache and Pragma: no-coche
		header("Cache-Control: ");
		header("Pragma: ");
		
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		
		require_once('models/ecommerce/ecommerce_product_image.php');
		$Image = new ecommerce_product_image();
		
		$products = $Product->getProductList();
		//print_r($products);exit;
		
		foreach ($products as $p) {
			$current = $Product->findProductInNode($p['id']);
			$product_node_data = $current[0];
			$page_id = $product_node_data['id'];
			$p['url'] = "http://{$_SERVER['HTTP_HOST']}" . $Node->getSeoURL($page_id);
			//$p['description'] = recode_string("html..utf8", $p['description']);
			$p['description'] = $p['name'];
			$this->tpl->assign('PRODUCT', $p);
			
			//image
			$images = $Image->listing("role = 'main' AND node_id=".$p['id'], "priority DESC, id ASC", '0,1');
			$this->tpl->assign('IMAGE_PRODUCT', "http://{$_SERVER['HTTP_HOST']}/image/{$images[0]['src']}");
		
			if (is_array($p['variety'])) {
				foreach ($p['variety'] as $v) {
					//$v['description'] = html_entity_decode($v['description']);
					$this->tpl->assign('VARIETY', $v);
					$this->tpl->assign('PRICE', $v['price']['value']);
					if ($p['publish'] == 1) $this->tpl->parse("content.item");
				}
			}
		}

		return true;
	}
}
