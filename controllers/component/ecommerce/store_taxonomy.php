<?php
/**
 * Copyright (c) 2013-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/ecommerce/ecommerce_store.php');
require_once('models/ecommerce/ecommerce_store_taxonomy.php');
require_once('models/common/common_taxonomy_tree.php');

class Onxshop_Controller_Component_Ecommerce_Store_Taxonomy extends Onxshop_Controller {

	/**
	 * main action
	 */
	public function mainAction()
	{
		// initiate
		$Store = new ecommerce_store();
		$Taxonomy_Tree = new common_taxonomy_tree();

		// input
		$store_id = $this->GET['store_id'];
		if (!is_numeric($store_id)) return false;

		// get list
		$taxonomy = $Taxonomy_Tree->getRelatedTaxonomy($store_id, "ecommerce_store_taxonomy");

		if (count($taxonomy) > 0) {
			foreach ($taxonomy as $category) {
				if ($category['publish'] == 1 && $category['parent'] == ONXSHOP_STORE_FACILITY_TAXONOMY_ID) {
					$this->tpl->assign("CATEGORY", $category);
					$this->tpl->parse("content.category");
				}
			}
		}

		return true;
	}
}

