<?php
/**
 * Copyright (c) 2006-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/page/default.php');

class Onxshop_Controller_Node_Page_Product extends Onxshop_Controller_Node_Page_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * initialize
		 */
		 
		require_once('models/ecommerce/ecommerce_product.php');
		require_once('models/common/common_node.php');
		$Node = new common_node();
		$Product = new ecommerce_product();
		
		/**
		 * get node detail
		 */
		 
		$node_data = $Node->nodeDetail($this->GET['id']);
		$product_id = $node_data['content'];
		
		
		/**
		 * Check if this is a product homepage
		 */
		 
		$product_node_homepage = $Node->getProductNodeHomepage($product_id);
		
		if ($node_data['id'] != $product_node_homepage['id']) {
			//forward to homepage
			$link = $Node->getSeoURL($product_node_homepage['id']);
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: http://{$_SERVER['SERVER_NAME']}{$link}");
			exit;
		}
		
		/**
		 * get simple product detail (just for basic product attributes)
		 */
		 
		$simple_product_detail = $Product->detail($product_id);
		
		/**
		 * get taxonomy_class
		 */
		 
		$related_taxonomy = $Product->getRelatedTaxonomy($product_id);
		$simple_product_detail['taxonomy_class'] = $this->createTaxonomyClass($related_taxonomy);
		
		/**
		 * save product taxonomy_class to registry
		 */
		
		$this->saveBodyCssClass($simple_product_detail['taxonomy_class']);
		
		/**
		 * assign simple product data to template
		 */
		
		$this->tpl->assign("PRODUCT", $simple_product_detail);
		
		
		/**
		 * varieties
		 */

		//$Variety_list = new Onxshop_Request("component/ecommerce/variety_list_ajax~product_id={$product_id}~");
		//$this->tpl->assign('VARIETY_LIST', $Variety_list->getContent());
		
		/**
		 * taxonomy
		 */
		
		$_Onxshop_Request = new Onxshop_Request("component/taxonomy~relation=product:id={$product_id}:hide_root=1~");
		$this->tpl->assign('TAXONOMY', $_Onxshop_Request->getContent());
				
		/**
		 * other product attributes
		 */
		 
		$_Onxshop_Request = new Onxshop_Request("component/ecommerce/product_other_data~id={$product_id}~");
		$this->tpl->assign('OTHER_DATA', $_Onxshop_Request->getContent());
		
		/**
		 * product reviews
		 */
		 
		$_Onxshop_Request = new Onxshop_Request("component/ecommerce/product_review~node_id={$product_id}:allow_anonymouse_submit=0~");
		$this->tpl->assign('PRODUCT_REVIEW', $_Onxshop_Request->getContent());
		
		
		/**
		 * standard page actions
		 */
		 
		$this->processContainers();
		$this->processPage();

		if (strlen($simple_product_detail['name_aka']) > 0)
			$this->tpl->parse('content.name_aka');

		/**
		 * everything went well
		 */
		 
		return true;
	}
	
	/**
	 * hook before parsing
	 */
	 
	public function parseContentTagsBeforeHook() {
		
		/**
		 * set active pages
		 */
		 
		$this->setActivePages();
		

		/**
		 * pass GET.product_id into template
		 */
		 
		require_once('models/common/common_node.php');
		$Node = new common_node();
		$node_data = $Node->nodeDetail($this->GET['id']);
		$this->GET['product_id'] = $node_data['content'];

		/**
		 * pass GET.image_width into template
		 */

		//include product image conf
		require_once('models/ecommerce/ecommerce_product_image.php');
		$ecommerce_product_image_conf = ecommerce_product_image::initConfiguration();

		if (is_numeric($this->GET['image_width'])) $image_width = $this->GET['image_width'];
		else $image_width = $GLOBALS['onxshop_conf']['global']['product_detail_image_width'];
		
		$this->GET['image_width'] = $image_width;
		
		return true;
	}
	
	/**
	 * getOpenGraphImage
	 */
	 
	public function getOpenGraphImage($node_id, $content = false) {
	
		if (is_numeric($content)) {
			
			$product_id = $content;
			
			require_once('models/ecommerce/ecommerce_product_image.php');
			$Image = new ecommerce_product_image();

			$image_list = $Image->listFiles($product_id);
			
			if (is_array($image_list) && count($image_list) > 0) return $image_list[0];
		
		} else {
			
			// Option to implement og:image associated to common_node record
			
		}

		return false;

	}
	
}
