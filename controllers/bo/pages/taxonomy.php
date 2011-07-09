<?php
/**
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Pages_Taxonomy extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		/*
		require_once('models/common/common_taxonomy.php');
		
		$Taxonomy = new common_taxonomy();
		
		$id = (is_numeric($this->GET['id'])) ? $this->GET['id'] : 0;
		
		$taxonomy_data = $Taxonomy->detail($id);
		
		$this->tpl->assign('TAXONOMY', $taxonomy_data);
		
		*/
		if (is_numeric($this->GET['id'])) $this->tpl->parse('content.submenu');

		return true;
	}
}
