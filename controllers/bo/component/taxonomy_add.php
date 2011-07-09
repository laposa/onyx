<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


class Onxshop_Controller_Bo_Component_Taxonomy_Add extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/common/common_taxonomy.php');
		$Taxonomy = new common_taxonomy();
		
		if ($_POST['save']) {	
			
			if($id = $Taxonomy->labelInsert($_POST['taxonomy']['label'])) {
				msg("Taxonomy label inserted.");
			} else {
				$this->tpl->assign('TAXONOMY', $_POST['taxonomy']);
			}
		}

		return true;
	}
}

