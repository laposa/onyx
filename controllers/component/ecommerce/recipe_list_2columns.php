<?php
/**
 * Copyright (c) 2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/recipe_list.php');

class Onxshop_Controller_Component_Ecommerce_Recipe_List_2columns extends Onxshop_Controller_Component_Ecommerce_Recipe_List {

	/**
	 * Parse recipe list items
	 */
	public function parseItems(&$list)
	{
		foreach ($list as $k=>$item) {
			
			$pos = $k+1;
			
			if ($pos%2 == 1) $column_num = 'One';
			else $column_num = 'Two';
			
			$this->tpl->assign('COLUMN_NUM', $column_num);
			
			$this->parseItem($item, 'content.layout.item');
			
			if ($pos%2 == 0) $this->tpl->parse('content.layout');
		
		}
		
	}

}
