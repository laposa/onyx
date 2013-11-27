<?php
/**
 * Copyright (c) 2009-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * this model doesn't have data on it's own, but in two separata tables
 * transition to joining common_taxonomy_tree with common_taxonomy_label
 * - MAYBE MAYBE!!! think about the concept more...
 *
 *
 */

class common_taxonomy {
	
	/**
	 * init
	 */

	public function __construct() {
		$this->initTaxonomy();
		
		return true;
	}

	/**
	 * init
	 */
	
	function initTaxonomy() {
		require_once('models/common/common_taxonomy_label.php');
		require_once('models/common/common_taxonomy_tree.php');
		$this->TaxonomyLabel = new common_taxonomy_label();
		$this->TaxonomyTree = new common_taxonomy_tree();
	}
	
	/**
	 * taxonomy item detail
	 */
	 
	public function taxonomyItemDetail($id) {
	
		if (!is_numeric($id)) return false;
		
		$item_detail = $this->TaxonomyTree->detailFull($id);
		
		return $item_detail;
	}

	/**
	 * label detail
	 */
	
	function labelDetail($id, $image_role = false) {
		
		$label_data = $this->TaxonomyLabel->detail($id);
		$label_data['image'] = $this->getLabelImages($label_data['id'], $image_role);
		
		return $label_data;
	}

	/**
	 * label detail by LabelToTree?
	 */

	function labelDetailByLTT($id) {
		$ltl_data = $this->TaxonomyTree->detail($id);
		return $this->labelDetail($ltl_data['label_id']);
	}

	/**
	 * insert label into taxonomy_label table and link into taxonomy_tree table
	 */
	
	function labelInsert($label_data) {
		
		$label_data['publish'] = 1;
		
		$label_data_clean = $label_data;
		unset($label_data_clean['parent']);
		unset($label_data_clean['priority']);
		
		if ($id = $this->TaxonomyLabel->insert($label_data_clean)) {
		
			$ltree_data['label_id'] = $id;
			
			//leave as null if not provided (root is NULL)
			if ($label_data['parent']) $ltree_data['parent'] = $label_data['parent'];
			
			if (is_numeric($label_data['priority'])) $ltree_data['priority'] = $label_data['priority'];
			else $ltree_data['priority'] = 0;
			
			$ltree_data['publish'] = $label_data['publish'];

			if ($tree_id = $this->labelLink($ltree_data)) {
				return $tree_id;
			} else {
				msg("Label was inserted, but cannot insert label into the tree", "error");
				return $id;
			}
			
		} else {
			return false;
		}
	}
	
	/**
	 * update label
	 */

	function labelUpdate($label_data) {
		
		if ($this->TaxonomyLabel->update($label_data)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * list labels
	 */
	
	function getLabels() {
		$labels = $this->TaxonomyLabel->listing();
		return $labels;
	}

	/**
	 * link label in taxonomy_tree
	 */
	
	function labelLink($ltl_data) {

		if ($id = $this->TaxonomyTree->insert($ltl_data)) return $id;
		else return false;
	}
	
	/**
	 * get tree
	 */
	
	function getTree($publish = 0) {
		
		$list = $this->TaxonomyTree->getTree($publish);

		return $list;
	
	}
	
	/**
	 * get children
	 */
	
	function getChildren($parent_id, $sort = 'priority DESC, id ASC', $published_only = false, $image_role = false) {
		
		if (!is_numeric($parent_id)) return false;
		
		// publish attribute on tree can be ignored, use publish attribute on label instead
		$list = $this->TaxonomyTree->listing("parent = " . $parent_id, $sort);

		$list_filtered = array();
		
		foreach ($list as $k=>$item) {

			$item['label'] = $this->labelDetail($item['label_id'], $image_role);
			
			if ($published_only && $item['label']['publish'] == 1) $list_filtered[] = $item;
			else if ($published_only == false) $list_filtered[] = $item;

		}
		
		return $list_filtered;
	
	}
	
	/**
	 * move item
	 */
	
	function moveItem($source_node_id, $destination_node_id, $position) {
		
		return $this->TaxonomyTree->moveItem($source_node_id, $destination_node_id, $position);
		
	}
	
	/**
	 * getRelatedTaxonomy
	 */
	 
	public function getRelatedTaxonomy($node_id, $relation = 'common_node_taxonomy') {
	
		return $this->TaxonomyTree->getRelatedTaxonomy($node_id, $relation);
	
	}
	
	/**
	 * getLabelImages
	 */
	 
	public function getLabelImages($label_id, $role = false) {
		
		if (!is_numeric($label_id)) return false;
		
		return $this->TaxonomyLabel->getImages($label_id, $role);
		
	}
}

