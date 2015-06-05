<?php
/** 
 * Copyright (c) 2006-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Taxonomy_Edit extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/common/common_taxonomy.php');
		$Taxonomy = new common_taxonomy();
		
		if ($_POST['save']) {
			if ($_POST['taxonomy']['label']['publish'] == 'on' || $_POST['taxonomy']['label']['publish'] == 1) $_POST['taxonomy']['label']['publish'] = 1;
		    else $_POST['taxonomy']['label']['publish'] = 0;
		    	
			if($Taxonomy->labelUpdate($_POST['taxonomy']['label'])) {
				msg("Taxonomy label updated.");
			}
		}
		if (is_numeric($this->GET['id'])) {
			$taxonomy_data['label'] = $Taxonomy->labelDetailByLTT($this->GET['id']);
			
			//display
			if ($taxonomy_data['label']['publish'] == 1) {
				$taxonomy_data['label']['publish_check'] = 'checked="checked"';
			} else {
				$taxonomy_data['label']['publish_check'] = 0;
			}
				
			$this->tpl->assign('TAXONOMY', $taxonomy_data);
			$this->tpl->parse('content.editor');
		} else {
			
		}

		return true;
	}
}
