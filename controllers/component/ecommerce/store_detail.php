<?php
/**
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/ecommerce/ecommerce_store.php');
require_once('models/ecommerce/ecommerce_store_taxonomy.php');
require_once('models/common/common_taxonomy_tree.php');

class Onxshop_Controller_Component_Ecommerce_Store_Detail extends Onxshop_Controller {

	/**
	 * main action
	 */
	public function mainAction()
	{
		$Store = new ecommerce_store();
		$Store_Taxonomy = new ecommerce_store_taxonomy();
		$Taxonomy_Tree = new common_taxonomy_tree();

		$node_id = (int) $this->GET['node_id'];
		$store = $Store->findStoreByNode($node_id);
		$taxonomy = $Taxonomy_Tree->getRelatedTaxonomy($store['id'], "ecommerce_store_taxonomy");

		if ($store) {

			if (count($taxonomy) > 0) {
				foreach ($taxonomy as $category) {
					if ($category['publish'] == 1) {
						$this->tpl->assign("CATEGORY", $category);
						$this->tpl->parse("content.store.category");
					}
				}
			}

			$this->tpl->assign("STORE", $store);
			$this->tpl->parse("content.store");

		} else {

			$this->tpl->parse("content.no_store");

		}

		return true;
	}
}

