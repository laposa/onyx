<?php
/**
 * Backoffice product list controller
 *
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Recipe_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		// initialize filter variables
		$taxonomy_id = $this->GET['taxonomy_tree_id'];
		if (isset($_POST['recipe-list-filter'])) $_SESSION['recipe-list-filter'] = $_POST['recipe-list-filter'];
		$keyword = $_SESSION['recipe-list-filter']['keyword'];

		// initialize sorting variables
		if ($this->GET['recipe-list-sort-by']) $_SESSION['recipe-list-sort-by'] = $this->GET['recipe-list-sort-by'];
		if ($this->GET['recipe-list-sort-direction']) $_SESSION['recipe-list-sort-direction'] = $this->GET['recipe-list-sort-direction'];

		$order_by = $_SESSION['recipe-list-sort-by'];
		$order_dir = $_SESSION['recipe-list-sort-direction'];

		// initialize pagination variables
		if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
		else $from = 0;
		if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
		else $per_page = 25;

		// get the list
		require_once('models/ecommerce/ecommerce_recipe.php');
		$Recipe = new ecommerce_recipe();	
		$recipe_list = $Recipe->getFilteredRecipeList($taxonomy_id, $keyword, $order_by, $order_dir, $per_page, $from);
		
		if (!is_array($recipe_list)) return false;

		if (count($recipe_list) == 0) {
			$this->tpl->parse('content.empty_list');
			return true;
		}

		// display pagination
		$count = count($recipe_list);
		$_nSite = new nSite("component/pagination~link=/request/bo/component/ecommerce/recipe_list:limit_from=$from:limit_per_page=$per_page:count=$count~");
		$this->tpl->assign('PAGINATION', $_nSite->getContent());

		// parse items
		foreach ($recipe_list as $item) {

			$item['modified'] = date("d/m/Y H:i", strtotime($item['modified']));
			$this->tpl->assign('ITEM', $item);
			if ($item['image_src']) $this->tpl->parse('content.list.item.image');
			
			$even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';
			$this->tpl->assign('CLASS', "class='$even_odd fullproduct'");

			$this->tpl->parse('content.list.item');
		}
		
		$this->tpl->parse('content.list');

		return true;
		
	}
}
