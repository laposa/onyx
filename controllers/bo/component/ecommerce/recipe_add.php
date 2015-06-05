<?php
/** 
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_node.php');
require_once('models/ecommerce/ecommerce_recipe.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Recipe_Add extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {

		$Recipe = new ecommerce_recipe();

		$recipe_data = $_POST['recipe'];
		$page_node_id = $recipe_data['page_node_id'];
		unset($recipe_data['page_node_id']);

		if ($_POST['save']) {
			if ($id = $Recipe->insertRecipe($recipe_data)) {

				$recipe_homepage = $this->insertNewRecipeToNode($id, $page_node_id);

				msg("Recipe has been added.");
				onxshopGoTo("backoffice/recipes/$id/edit");
			} else {
				msg("Adding of Recipe Failed.", 'error');
			}
		}
		$recipe_data['page_node_id'] = (int) $_SESSION['active_pages'][0];
		$this->tpl->assign('RECIPE', $recipe_data);

		return true;
	}

	/**
	 * insert recipe to node
	 */
	
	function insertNewRecipeToNode($recipe_id, $parent_id) {
	
		if (!is_numeric($recipe_id)) return false;
		if (!is_numeric($parent_id)) return false;
		
		$Node = new common_node();
		$Recipe = new ecommerce_recipe();
		
		/**
		 * get recipe detail
		 */
		 
		$recipe_detail = $Recipe->detail($recipe_id);
		 
		/**
		 * prepare node data
		 */
		 
		$recipe_node['title'] = $recipe_detail['title'];
		$recipe_node['parent'] = $parent_id;
		$recipe_node['parent_container'] = 0;
		$recipe_node['node_group'] = 'page';
		$recipe_node['node_controller'] = 'recipe';
		$recipe_node['content'] = $recipe_id;
		//$recipe_node['layout_style'] = $Node->conf['page_recipe_layout_style'];
		//this need to be updated on each recipe update
		$recipe_node['priority'] = $recipe_detail['priority'];
		$recipe_node['publish'] = $recipe_detail['publish'];

		/**
		 * insert node
		 */
		 
		if ($recipe_homepage = $Node->nodeInsert($recipe_node)) {
			return $recipe_homepage;
		} else {
			msg("Can't add recipe to node.");
			return false;
		}
		
	}
}
