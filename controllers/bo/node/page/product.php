<?php
/**
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Page_Product extends Onxshop_Controller_Bo_Node_Default {

	/**
	 * post action
	 */

	function post() {
		//$Product_detail = new nSite("component/ecommerce/product_detail~product_id={$this->node_data['content']}:template_block=product_more~");
		//$this->tpl->assign("PRODUCT_DETAIL", $Product_detail->getContent());
	}
}
