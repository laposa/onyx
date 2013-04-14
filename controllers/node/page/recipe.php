<?php
/**
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/page/default.php');
require_once('models/common/common_node.php');
require_once('models/ecommerce/ecommerce_recipe.php');
require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');

class Onxshop_Controller_Node_Page_Recipe extends Onxshop_Controller_Node_Page_Default {

	/**
	 * main action
	 */
	public function mainAction()
	{
		$this->recipeDetail($this->GET['recipe_id']);
		
		/**
		 * standard page actions
		 */
		 
		$this->processContainers();
		$this->processPage();
		
		return true;
	}
	
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
		$this->GET['taxonomy_tree_id'] = implode(",", $taxonomy_ids);

	}
	
	/**
	 * recipeDetail
	 */
	 
	public function recipeDetail($recipe_id) {
		
		if (!is_numeric($recipe_id)) return false;
		
		$Recipe = new ecommerce_recipe();
		$recipe = $Recipe->getDetail($recipe_id);

		if ($recipe) {
		
			/**
			 * get taxonomy_class
			 */
			 
			$related_taxonomy = $Recipe->getRelatedTaxonomy($recipe_id);
			$recipe['taxonomy_class'] = $this->createTaxonomyClass($related_taxonomy);
			
			/**
			 * save product taxonomy_class to registry
			 */
			
			$this->saveBodyCssClass($simple_product_detail['taxonomy_class']);
			
			/**
			 * singular/plurar text
			 */
			 			
			$recipe['persons'] = $recipe['serving_people'] == 1 ? 'person' : 'persons';
			$recipe['minutes'] = $recipe['cooking_time'] == 1 ? 'minute' : 'minutes';

			$this->tpl->assign("RECIPE", $recipe);

		}

		return true;
	}
}