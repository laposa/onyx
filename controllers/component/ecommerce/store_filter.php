<?php
/**
 * Copyright (c) 2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/ecommerce/ecommerce_store.php');
require_once('models/ecommerce/ecommerce_store_taxonomy.php');
require_once('models/common/common_taxonomy_tree.php');
require_once('models/common/common_taxonomy.php');

class Onxshop_Controller_Component_Ecommerce_Store_Filter extends Onxshop_Controller {

	/**
	 * main action
	 */
	public function mainAction()
	{
		// initiate
		$Store = new ecommerce_store();
		$Taxonomy_Tree = new common_taxonomy_tree();
		$Taxonomy = new common_taxonomy();

		$taxonomy_all_ids = array();
		
		// input
		$store_id = $this->GET['store_id']; // if provided, filter will pre-select categories assigned to selected store
		$keyword = $_GET['keyword'];
		
		// get related list
		if (is_numeric($store_id)) {
			
			$taxonomy_related = $Taxonomy_Tree->getRelatedTaxonomy($store_id, "ecommerce_store_taxonomy");
	
			if (count($taxonomy_related) > 0) {
				
				
				foreach ($taxonomy_related as $category) {
					
					$taxonomy_all_ids[] = $category['id'];
					
					if ($category['publish'] == 1 && $category['parent'] == ONXSHOP_STORE_FACILITY_TAXONOMY_ID) {
						$this->tpl->assign("CATEGORY", $category);
						$this->tpl->parse("content.category");
					}
				}
			}
		
		}

		// get all taxonomy list
		$taxonomy_all = $Taxonomy->getChildren(ONXSHOP_STORE_FACILITY_TAXONOMY_ID, 'priority DESC, id ASC', true);
		
		foreach ($taxonomy_all as $item) {
			
			if (in_array($item['id'], $taxonomy_all_ids)) $this->tpl->assign('SELECTED', 'selected');
			else $this->tpl->assign('SELECTED', '');
			
			$this->tpl->assign('ITEM', $item);
			$this->tpl->parse('content.item');
		}
		
		// $keyword
		if (trim($keyword) != '') {
			$this->tpl->assign('KEYWORD', $keyword);
		}
		
		return true;
	}

}

