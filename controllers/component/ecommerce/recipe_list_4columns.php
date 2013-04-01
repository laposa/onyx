<?php
/**
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/recipe_list.php');

class Onxshop_Controller_Component_Ecommerce_Recipe_List_4columns extends Onxshop_Controller_Component_Ecommerce_Recipe_List {

	/**
	 * Parse recipe list items
	 */
	public function parseItems(&$list)
	{
		$columnNames = array("One", "Two", "Three", "Four");

		for ($j = 0; $j < 4; $j++) {
			for ($i = $j; $i < count($list); $i += 4) {
				$item = $list[$i];
				$this->tpl->assign("ITEM", $item);
				$this->tpl->parse("content.column.item");
			}
			$this->tpl->assign("COLUMN_NUM", $columnNames[$j]);
			$this->tpl->parse("content.column");
		}
	}

}
