<?php
/**
 * Copyright (c) 2010-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/ecommerce/ecommerce_recipe.php');

class Onxshop_Controller_Component_Recipe_List extends Onxshop_Controller {

	/**
	 * main action (only a router in this case)
	 */

	public function mainAction()
	{
		// init models
		$Recipe = new ecommerce_recipe();

		// get taxonomy ids
		$taxonomy_ids = explode(",", $this->GET['taxonomy_ids']);

		// validate input
		if (!is_array($taxonomy_ids)) return false;
		foreach ($taxonomy_ids as $taxonomy_id) 
			if (!is_numeric($taxonomy_id)) return false;

		// is there a limit?
		if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
		else $from = 0;
		if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
		else $per_page = 100;

		$limit = "$from,$per_page";

		$list = $Recipe->getRecipeListForTaxonomy($taxonomy_ids, "", $limit);
		$this->parseItems($list);

		return true;
	}

	/**
	 * Parse recipe list items
	 */
	public function parseItems(&$list)
	{
		foreach ($list as $i => $item) {
			$this->tpl->assign("ITEM", $item);
			$this->tpl->parse("content.item");
		}
	}

}
