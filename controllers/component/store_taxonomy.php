<?php
/**
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/ecommerce/ecommerce_store.php');
require_once('models/ecommerce/ecommerce_store_taxonomy.php');
require_once('models/common/common_taxonomy_tree.php');

class Onxshop_Controller_Component_Store_Taxonomy extends Onxshop_Controller {

	/**
	 * main action
	 */
	public function mainAction()
	{
		$Store = new ecommerce_store();
		$Taxonomy_Tree = new common_taxonomy_tree();

		$store_id = $this->GET['store_id'];
		if (!is_numeric($store_id)) return false;

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

