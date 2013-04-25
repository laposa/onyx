<?php
/**
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Content_Product_highlights extends Onxshop_Controller_Bo_Node_Default {

	/**
	 * pre action
	 */

	function pre() {

		if ($_POST['node']['component']['display_sorting'] == 'on') $_POST['node']['component']['display_sorting'] = 1;
		else $_POST['node']['component']['display_sorting'] = 0;
		
		if ($_POST['node']['component']['display_pagination'] == 'on') $_POST['node']['component']['display_pagination'] = 1;
		else $_POST['node']['component']['display_pagination'] = 0;

		$this->parseProductSelect();
	}
	
	/**
	 * post action
	 */
	 
	function post() {
	
		//template
		$this->tpl->assign("SELECTED_template_{$this->node_data['component']['template']}", "selected='selected'");
		//image role
		$this->tpl->assign("SELECTED_image_role_{$this->node_data['component']['image_role']}", "selected='selected'");
		//sorting
		$this->node_data['component']['display_sorting'] = ($this->node_data['component']['display_sorting']) ? 'checked="checked"' : '';
		//pagination
		$this->node_data['component']['display_pagination'] = ($this->node_data['component']['display_pagination']) ? 'checked="checked"' : '';
		
		//find product in the node
		$current = $this->node_data['component']['related'];
		if (is_array($current)) {
			foreach ($current as $product_id) {
				//find product in the node
				if (is_numeric($product_id)) {
					$detail = $this->Node->listing("node_group = 'page' AND node_controller = 'product' AND content = '$product_id'");
					$current = $detail[0];
					if ($current['publish'] == 0) $current['class'] = 'notpublic';
					$this->tpl->assign('CURRENT', $current);
					$this->tpl->parse('content.item');
				}
			}
		}
	}

	function parseProductSelect() {

		require_once('models/ecommerce/ecommerce_product.php');
		$Product = new ecommerce_product();
		$listing = $Product->listing('', 'name ASC');
		foreach ($listing as $item) {
			if ($item['publish'] == 0) $item['class'] = 'notpublic';
			$detail = $this->Node->listing("node_group = 'page' AND node_controller = 'product' AND content = '{$item['id']}'");			
			if (count($detail) == 0) $item['disabled'] = 'disabled';
			$this->tpl->assign("PRODUCT", $item);
			$this->tpl->parse("content.product_select_item");
		}
	}
}
