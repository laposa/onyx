<?php
/**
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
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
		 
		$simple_product_detail = $Product->detail($node_data['content']);
		$this->tpl->assign("PRODUCT", $simple_product_detail);
		
		
		/**
		 * varieties
		 */

		//$Variety_list = new nSite("component/ecommerce/variety_list_ajax~product_id={$product_id}~");
		//$this->tpl->assign('VARIETY_LIST', $Variety_list->getContent());
		
		/**
		 * taxonomy
		 */
		
		$_nSite = new nSite("component/taxonomy~relation=product:id={$product_id}:hide_root=1~");
		$this->tpl->assign('TAXONOMY', $_nSite->getContent());
				
		/**
		 * other product attributes
		 */
		 
		$_nSite = new nSite("component/ecommerce/product_other_data~id={$product_id}~");
		$this->tpl->assign('OTHER_DATA', $_nSite->getContent());
		
		/**
		 * product reviews
		 */
		 
		$_nSite = new nSite("component/ecommerce/review~node_id={$product_id}:allow_anonymouse_submit=0~");
		$this->tpl->assign('PRODUCT_REVIEW', $_nSite->getContent());
		
		
		/**
		 * standard page actions
		 */
		 
		$this->processContainers();
		$this->processPage();

		/**
		 * try to find a product_type_id specific template block or use master block (product_master)
		 */
		 
		$product_type_id_specific_block_name = "content.product_type_id_{$simple_product_detail['product_type_id']}";
		if ($this->_checkTemplateBlockExists($product_type_id_specific_block_name)) $this->tpl->parse($product_type_id_specific_block_name);
		else $this->tpl->parse('content.product_master');
		
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
}
