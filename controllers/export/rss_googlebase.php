<?php
/** 
 * Copyright (c) 2008-201 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */
 
//http://www.google.com/base/products.html

class Onxshop_Controller_Export_Rss_Googlebase extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		set_time_limit(0);
		
		header('Content-Type: text/xml; charset=UTF-8');
		
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		require_once('models/ecommerce/ecommerce_product.php');
		require_once('models/ecommerce/ecommerce_product_image.php');
		$Product = new ecommerce_product();
		$Image = new ecommerce_product_image();
		
		
		$products = $Product->getProductList();
		//print_r($products);exit;
		
		if (is_array($products)) {
			foreach ($products as $p) {
				if ($p['publish'] == 1) {
				
					$nodes = $Product->findProductInNode($p['id']);
					$link = $Node->getSeoURL($nodes[0]['id']);
					$p['link'] = "http://{$_SERVER['HTTP_HOST']}{$link}";
				
					
					$images = $Image->listing("role = 'main' AND node_id=".$p['id'], "priority DESC, id ASC");
					
					$p['image_src'] = "http://{$_SERVER['HTTP_HOST']}/image/{$images[0]['src']}";
					$this->tpl->assign('PRODUCT', $p);
					
					if (is_array($p['variety'])) {
						foreach ($p['variety'] as $k=>$v) {
							//show only first variety
							if ($k == 0) {
								$this->tpl->assign('VARIETY', $v);
								$this->tpl->assign('PRICE', $v['price']);
								
								//set availibity, other possible values are 'available for order' and 'preorder'
								//if ($v['stock'] > 0) $this->tpl->assign('AVAILIBILITY', 'in stock');
								//else $this->tpl->assign('AVAILIBILITY', 'out of stock');
								$this->tpl->assign('AVAILIBILITY', 'in stock'); //always on stock, because first variety can be out of stock, but other not, we should do a check if any variety is in stock
								
								$this->tpl->parse("content.generated.item");
							}
						}
					}
				}
			}
		}
		
		//save it to the file
		$this->tpl->parse("content.generated");
		file_put_contents(ONXSHOP_PROJECT_DIR . "var/files/googlebase.xml", $this->tpl->text("content.generated"));

		return true;
	}
}
