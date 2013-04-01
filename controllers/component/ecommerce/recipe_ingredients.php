<?php
/**
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/ecommerce/ecommerce_recipe_ingredients.php');

class Onxshop_Controller_Component_Ecommerce_Recipe_Ingredients extends Onxshop_Controller {

	/**
	 * main action
	 */
	public function mainAction()
	{
		$recipe_id = $this->GET['id'];

		if (!is_numeric($recipe_id)) return false;

		$Ingredients = new ecommerce_recipe_ingredients();
		$ingredients = $Ingredients->getIngredientsForRecipe($recipe_id);

		if (is_array($ingredients)) {

		$prevGroup = '';

			foreach ($ingredients as $i => $ingredient) {

				$ingredient['index'] = $i;

				$this->tpl->assign("INGREDIENT", $ingredient);

				if ($ingredient['group_title'] != $prevGroup)
					$this->tpl->parse("content.ingredient.group_title");

				if (strlen($ingredient['notes']) > 0) 
					$this->tpl->parse("content.ingredient.note");

				$this->tpl->parse("content.ingredient");

				$prevGroup = $ingredient['group_title'];
			}

		}

		return true;
	}
}

