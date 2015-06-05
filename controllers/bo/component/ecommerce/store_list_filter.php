<?php
/**
 * Backoffice store list filter
 *
 * Copyright (c) 2013-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */
require_once('models/ecommerce/ecommerce_store_type.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Store_List_Filter extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		if (isset($_POST['store-list-filter'])) $_SESSION['bo']['store-list-filter'] = $_POST['store-list-filter'];
		
		$filter = $_SESSION['bo']['store-list-filter'];
		$this->tpl->assign('FILTER', $filter);
		
		$this->parseTypeSelect($_SESSION['bo']['store-list-filter']['type_id']);

		return true;
	}
	
	/**
	 * parseTypeSelect
	 */

	protected function parseTypeSelect($selected_id)
	{
		$Type = new ecommerce_store_type();
		$records = $Type->listing();

		foreach ($records as $item) {
			if ($item['id'] == $selected_id) $item['selected'] = 'selected="selected"';
			$this->tpl->assign("ITEM", $item);
			$this->tpl->parse("content.type.item");
		}
		$this->tpl->parse("content.type");
	}

}
