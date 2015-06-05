<?php
/** 
 * Copyright (c) 2006-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Taxonomy extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * input variables
		 */
		 
		if (is_numeric($this->GET['id'])) $node_id =  $this->GET['id'];
		else return false;
		
		$relation = $this->GET['relation'];
		
		/**
		 * initialize related object
		 */
		 
		$this->Taxonomy = $this->initializeRelatedObject($relation);
		
		/**
		 * process listing
		 */
		 
		if (is_numeric($node_id) && is_object($this->Taxonomy)) {
		
			$taxonomy_list = $this->getTaxonomyListForNodeId($node_id);
			
			$taxonomy_label_list = $this->mapTaxonomyLabels($taxonomy_list);
			
			$taxonomy_label_level_list = $this->joinSameLevelTitles($taxonomy_label_list);
			
			$taxonomy_label_route_list = $this->joinRouteTitles($taxonomy_label_level_list);	
			
			$taxonomy_label_route_list = $this->addParentData($taxonomy_label_route_list);
			
			$taxonomy_label_route_list = $this->sortByPriority($taxonomy_label_route_list);
			
			$this->parseListToTemplate($taxonomy_label_route_list);
			
		}

		return true;
	}
	
	/**
	 * initialize related object
	 */
	 
	public function initializeRelatedObject($relation = 'node') {
	
		require_once('models/common/common_node.php');
		$Node = new common_node();
		
		switch ($relation) {
		
			case 'product':
				require_once('models/ecommerce/ecommerce_product_taxonomy.php');
				$Taxonomy = new ecommerce_product_taxonomy();
			break;
			
			case 'variety':
				require_once('models/ecommerce/ecommerce_product_variety_taxonomy.php');
				$Taxonomy = new ecommerce_product_variety_taxonomy();
			break;
			
			case 'node':
			default:
				require_once('models/common/common_node_taxonomy.php');
				$Taxonomy = new common_node_taxonomy();
			break;
			
		}
		
		return $Taxonomy;
	}
	
	/**
	 * map taxonomy labels
	 */
	 
	public function mapTaxonomyLabels($list) {
		
		if (!is_array($list)) return false;
		
		$label_list = array();
		
		foreach ($list as $item) {
			
			$label = $this->Taxonomy->getLabel($item['taxonomy_tree_id']);
			$label_list[$label['id']] = $label;
			
		}

		return $label_list;
			
	}
	
	/**
	 * get root
	 */
	 
	function getParentData($item) {
		
		if (is_numeric($item['parent'])) {
			
			$parent_data = $this->Taxonomy->getLabel($item['parent']);
			
		} else {
			//root category (NULL)
			$parent_id = 0;
			$detail['taxonomy_data']['parent'] = 0;
			$parent_data['id'] = 0;
			$parent_data['title'] = 'Category';
			$parent_data['description'] = '';
			$parent_data['priority'] = 0;
			$parent_data['priority'] = 1;
			$parent_data['label'] = $detail['parent_data'];
		}		
		
		return $parent_data;
			
	}
	
	/**
	 * parse to template
	 */
	 
	function parseListToTemplate($list) {
			
		if (!is_array($list)) return false;
		
		$parsed_count = 0;
		
		//print_r($taxonomy_detail);
		foreach ($list as $item) {
			
			$parsed_item = $this->parseItem($item);
			
			$parsed_count = $parsed_count + $parsed_item;
			
		}
		
		if ($parsed_count > 0) $this->tpl->parse('content.taxonomy');
			
	}
	
	/**
	 * parse item
	 */
	 
	public function parseItem($item) {
		
		//don't show root items in same cases
		
		if ($this->GET['hide_root'] == 0 || $item['parent'] > 0) {
			
			//display only published folders and items
			if ($item['parent_data']['label']['publish'] == 1 && $item['label']['publish'] == 1) {
				
				$this->tpl->assign("ITEM", $item);
				
				$this->tpl->parse("content.taxonomy.item");
			
				return 1;
				
			} else {
				
				return 0;
				
			}
			
			return 1;
			
		} else {
		
			return 0;
			
		}
		
	}
	
	
	/**
	 * get taxonomy list for node id
	 */
	 
	public function getTaxonomyListForNodeId($node_id) {
	
		if (!is_numeric($node_id)) return false;
		
		$list = $this->Taxonomy->listing("node_id = " . $node_id);
		
		return $list;
		
	}
	
	/**
	 * join together labels from the same level
	 */
	 
	function joinSameLevelTitles($list) {

		if (!is_array($list)) return false;
	
		$list_joined = array();
		$list_joined_final = array();
		
		$proceed_parent_id = array();
		
		foreach($list as $item) {
			
			if (in_array($item["parent"], $proceed_parent_id)) { 
				
				$list_joined[$item["parent"]]['label']['title'] =  $list_joined[$item["parent"]]['label']['title'] . ', ' . $item['label']['title'];
					
			} else {
					
				$list_joined[$item["parent"]] = $item;
						
				$proceed_parent_id[] = $item["parent"];
				
			}
					
		}
		
		//reformat
		
		foreach ($list_joined as $item) {
		
			$list_joined_final[$item['id']] = $item;
		
		}

		return $list_joined_final;

	}
	
	/**
	 * join together labels from the same route
	 */
	 
	function joinRouteTitles($list) {

		if (!is_array($list)) return false;
	
		$list_joined = array();
		$list_joined_final = array();
		$proceed_parent_id = array();
		
		foreach($list as $item) {
			
			if (is_array($list[$item["parent"]])) { 
				
				$proceed_parent_id[] = $item['parent']; //prepare to be removed	
				
				$item['label']['title'] = $list[$item['parent']]['label']['title'] . ', ' . $item['label']['title'];
				$item['parent'] = $list[$item["parent"]]['parent'];
				$item['publish'] = $list[$item["parent"]]['publish'];
				$item['priority'] = $list[$item["parent"]]['priority'];
				
			}
			
			$list_joined[] = $item;
				
		}

		//remove proceed parent items
		
		foreach ($list_joined as $item) {
		
			if (!in_array($item['id'], $proceed_parent_id)) $list_joined_final[] = $item;
		
		}
		
		return $list_joined_final;

	}
	
	/**
	 * add parent data
	 */
	 
	public function addParentData($list) {
	
		foreach ($list as $k=>$item) {
		
			$list[$k]['parent_data'] = $this->getParentData($item);
		
		}
		
		return $list;
	}
	
	
	/**
	 * sort by priority
	 */
	 
	public function sortByPriority($list) {
	
		/**
		 * first pass parent_priority to the same level in multiarray (preparation for multisort function)
		 */
		 
		foreach ($list as $k=>$item) {
		
			$list[$k]['parent_priority'] = $item['parent_data']['priority'];
		
		}
		
		/**
		 * use multisort function
		 */
		 
		$list = php_multisort($list, array(array('key'=>'parent_priority', 'sort'=>'DESC'), array('key'=>'parent', 'type'=>'numeric')));
		
		foreach ($list as $item) {
			$l[] = $item;
		}
		
		$list = $l;
				
				
		return $list;
	
	}
}
		
