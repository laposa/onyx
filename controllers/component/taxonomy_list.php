<?php
/** 
 * Copyright (c) 2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/taxonomy.php');

class Onxshop_Controller_Component_Taxonomy_List extends Onxshop_Controller_Component_Taxonomy {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		if (is_numeric($this->GET['parent'])) $parent = $this->GET['parent'];
		else $parent = 0;
		
		if ($this->GET['publish'] == 1) $published_only = true;
		else $published_only = false;
		
		require_once('models/common/common_taxonomy.php');
		$Taxonomy = new common_taxonomy();

		$list = $Taxonomy->getChildren($parent, 'priority DESC, id ASC', true);
		
		foreach ($list as $item) {
		
			/**
			 * image
			 */
			
			if (is_array($item['label']['image']) && count($item['label']['image']) > 0) {
				$image = $item['label']['image'][0];
				
				$this->tpl->assign('IMAGE', $image);
				if (is_numeric($image['link_to_node_id'])) $this->tpl->parse('content.item.image_link');
				else $this->tpl->parse('content.item.image');
			}
			
			/**
			 * content
			 */
			 
			$this->tpl->assign('ITEM', $item);
			$this->tpl->parse('content.item');
			
		}
		
		return true;
		
	}
	
}