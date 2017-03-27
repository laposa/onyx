<?php
/** 
 * Copyright (c) 2013-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/taxonomy.php');

class Onxshop_Controller_Component_Taxonomy_List extends Onxshop_Controller_Component_Taxonomy {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * input variables
		 */
		 
		if (is_numeric($this->GET['parent'])) $parent = $this->GET['parent'];
		else $parent = 0;
		
		if ($this->GET['publish'] == 1) $published_only = true;
		else $published_only = false;
		
		/* optional node_id and relation to show only related items (associated categories) */
		if (is_numeric($this->GET['node_id'])) $node_id =  $this->GET['node_id'];
		else $node_id = false;
		
		if ($this->GET['relation']) $relation = $this->GET['relation'];
		else $relation = false;
		
		if (is_numeric($node_id) && $relation) $related_taxonomy_label_list = $this->findOnlyRelated($node_id, $relation);
		else $related_taxonomy_label_list = false;
		
		if (is_numeric($this->GET['link_to_node_id'])) $link_to_node_id = $this->GET['link_to_node_id'];
		else $link_to_node_id = false;
		
		require_once('models/common/common_taxonomy.php');
		$Taxonomy = new common_taxonomy();

		$list = $Taxonomy->getChildren($parent, 'priority DESC, id ASC', true);
		
		foreach ($list as $item) {
			
			// process only if filtering by related isn't required or current item is matching
			if ($related_taxonomy_label_list === false || (is_array($related_taxonomy_label_list) && is_array($related_taxonomy_label_list[$item['label_id']]))) {
				
				$this->assignAndParseItem($item, $link_to_node_id);
				
			}
		}
		
		return true;
		
	}
	
	/**
	 * assignAndParseItem
	 */
	 
	public function assignAndParseItem($item, $link_to_node_id) {

		if (!is_array($item)) return false;
		
		if (is_numeric($link_to_node_id)) $item['link_to_node_id'] = $link_to_node_id;
		
		$this->tpl->assign('ITEM', $item);
				
		/**
		 * image
		 */
		
		if (is_array($item['label']['image']) && count($item['label']['image']) > 0) {
			$image = $item['label']['image'][0];
			
			$this->tpl->assign('IMAGE', $image);
			if (is_numeric($image['link_to_node_id'])) $this->tpl->parse('content.item.image_link');
			else $this->tpl->parse('content.item.image');
			
		} else {
			
			if (is_numeric($link_to_node_id)) $this->tpl->parse('content.item.text_link');
			else $this->tpl->parse('content.item.text');
		}
		
		/**
		 * content
		 */
		 
		$this->tpl->parse('content.item');
		
	}
	
	/**
	 * findOnlyRelated
	 */
	
	public function findOnlyRelated($node_id, $relation) {
		
		if (!is_numeric($node_id)) return false;
		if (!$relation) return false;
		
		/**
		 * initialize related object
		 */
		 
		$this->Taxonomy = $this->initializeRelatedObject($relation);
		
		$taxonomy_list = $this->getTaxonomyListForNodeId($node_id);
		$taxonomy_label_list = $this->mapTaxonomyLabels($taxonomy_list);
		
		return $taxonomy_label_list;
		
	}
	
}
