<?php
/**
 * Taxonomy tree
 *
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/menu_js.php');

class Onxshop_Controller_Component_Taxonomy_Menu extends Onxshop_Controller_Component_Menu_Js {

	/**
	 * get tree
	 */
	public function getTree($publish = 1) {

		$list = $this->getList($publish);

		return $this->buildTree($list);
	}

	/**
	 * get list
	 */
	 
	public function getList($publish = 1) {
		
		require_once('models/common/common_taxonomy.php');
		$Taxonomy = new common_taxonomy();
		
		$list = $Taxonomy->getTree($publish);

		return $list;
	}
}
