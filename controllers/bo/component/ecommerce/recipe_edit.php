<?php
/** 
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
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
		
			// update recipe
			if($recipe_id = $Recipe->updateRecipe($_POST['recipe'])) {
			
				msg("Recipe ID=$recipe_id updated");
				
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
			
