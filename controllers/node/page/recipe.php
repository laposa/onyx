<?php
/**
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/page/default.php');
require_once('models/common/common_node.php');
require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');

class Onxshop_Controller_Node_Page_Recipe extends Onxshop_Controller_Node_Page_Default {

	/**
	 * hook before parsing
	 */
	 
	public function parseContentTagsBeforeHook()
	{

		parent::parseContentTagsBeforeHook();

		/**
		 * pass GET.recipe_id into template
		 */
		 
		$Node = new common_node();
		$node_data = $Node->nodeDetail($this->GET['id']);
		$this->GET['recipe_id'] = $node_data['content'];

		/**
		 * pass GET.taxonomy_ids into template
		 */
		 
		$Recipe_Taxonomy = new ecommerce_recipe_taxonomy();
		$taxonomy_ids = $Recipe_Taxonomy->getRelationsToRecipe($this->GET['recipe_id']);
		$this->GET['taxonomy_ids'] = implode(",", $taxonomy_ids);

	}
}