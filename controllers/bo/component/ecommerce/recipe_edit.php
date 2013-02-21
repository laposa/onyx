<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Recipe_Edit extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		// initialize
		require_once('models/ecommerce/ecommerce_recipe.php');
		$Recipe = new ecommerce_recipe();
		
		// save		 
		if ($_POST['save']) {

			// set values
			if (!isset($_POST['recipe']['publish'])) $_POST['recipe']['publish'] = 0;
			$_POST['recipe']['modified'] = date('c');
			
			// handle other_data
			$_POST['recipe']['other_data'] = serialize($_POST['recipe']['other_data']);
			
			// update recipe
			if($id = $Recipe->update($_POST['recipe'])) {
			
				msg("Recipe ID=$id updated");
			
				// update node info (if exists)
				$recipe_homepage = $Recipe->getRecipeHomepage($_POST['recipe']['id']);
			
				if (is_array($recipe_homepage) && count($recipe_homepage) > 0) {
					$recipe_homepage['publish'] = $_POST['recipe']['publish'];
					
					require_once('models/common/common_node.php');
					$Node = new common_node();
					
					$Node->nodeUpdate($recipe_homepage);
					
				}
				
				// forward to recipe list main page and exit
				onxshopGoTo("/backoffice/recipes");
				return true;
			}
		}
		
		// recipe detail
		$recipe = $Recipe->detail($this->GET['id']);
		$recipe['publish'] = ($recipe['publish'] == 1) ? 'checked="checked" ' : '';
		$this->tpl->assign('RECIPE', $recipe);

		return true;
	}
}	
			
