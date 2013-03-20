<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/ecommerce/ecommerce_recipe.php');

class Onxshop_Controller_Component_Recipe_Detail extends Onxshop_Controller {

	/**
	 * main action
	 */
	public function mainAction()
	{
		$recipe_id = $this->GET['id'];

		if (!is_numeric($recipe_id)) return false;

		$Recipe = new ecommerce_recipe();
		$recipe = $Recipe->detail($recipe_id);

		if ($recipe) {

			$recipe['persons'] = $recipe['serving_people'] == 1 ? 'person' : 'persons';
			$recipe['minutes'] = $recipe['cooking_time'] == 1 ? 'minute' : 'minutes';
			$this->tpl->assign("RECIPE", $recipe);

		}

		return true;
	}
}

