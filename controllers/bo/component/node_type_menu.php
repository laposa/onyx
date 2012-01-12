<?php
/** 
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/menu.php');

class Onxshop_Controller_Bo_Component_Node_Type_Menu extends Onxshop_Controller_Component_Menu {
	
	/**
	 * main action
	 */
	
	public function mainAction() {
	
		$list = $this->getList($publish);
		$list = $this->filterAndAssignInfo($list);
		$md_tree = $this->buildTree($list, $node_id);
		$this->generateSelectMenu($md_tree);
		
		return true;
	} 

	/**
	 * generate SELECT menu
	 */
	
	public function generateSelectMenu($md_tree) {
	
		/**
		 * retrieve template_info
		 */
		
		$templates_info = $this->retrieveTemplateInfo();
		
		
		/**
		 * reorder
		 */
		 
		$md_tree = $this->reorder($md_tree);
		
		
		if (!is_array($md_tree)) return false;
		
		/**
		 * iterate through each item
		 */
		 
		$this->iterateThroughGroups($md_tree);
		
		return true;
	}
	
	/**
	 * iterate throught groups
	 */
	 
	public function iterateThroughGroups($list) {
	
		foreach ($list as $group) {
					
			/**
			 * display only what requested, but for content and layout both
			 */
			 
			if (
				$this->GET['expand_all'] == 1 || 
				$group['name'] == $this->GET['node_group'] ||
				($group['name'] == 'layout' && $this->GET['node_group'] == 'content')
			) {				
				$this->tpl->assign('GROUP', $group);
				
				if (is_array($group['children']) && count($group['children']) > 0) {
				
					$this->iterateThroughItems($group['children']);
				}
				
				$this->tpl->parse("content.group");
			}
			
		}

	}
	
	/**
	 * iterate throught items
	 */
	 
	public function iterateThroughItems($list) {
		
		foreach ($list as $item) {
			
			if ($item['selected']) $item['selected'] = "selected='selected'";
			else $item['selected'] = '';
			
			$this->tpl->assign('ITEM', $item);
			$this->tpl->parse("content.group.item");
		}
	}
	
	
	/**
	 * get md array for node directory
	 */
	
	function getList($publish = 1) {
	
		require_once('models/common/common_file.php');
		$File = new common_file();
		
		//getting list of templates, joing project and onxshop node dir
		$list = $File->getFlatArrayFromFsJoin("templates/node/");
		
		//remove .html, .php
		foreach ($list as $k=>$item) {
			$list[$k]['name'] = preg_replace('/\.html$/', '', $list[$k]['name']);
			$list[$k]['name'] = preg_replace('/\.php$/', '', $list[$k]['name']);
			$list[$k]['id'] = preg_replace('/\.html$/', '', $list[$k]['id']);
			$list[$k]['id'] = preg_replace('/\.php$/', '', $list[$k]['id']);
			$list[$k]['parent'] = preg_replace('/\.html$/', '', $list[$k]['parent']);
			$list[$k]['parent'] = preg_replace('/\.php$/', '', $list[$k]['parent']);
		}
				
		return $list;
	}
	
	
	/**
	 * reorder file list
	 */
	 
	public function reorder($md_tree) {
		
		//make sure array is sorted
		//print_r($md_tree);
		array_multisort($md_tree);
		//print_r($md_tree);
		
		//reorder
		$temp = array();
		$temp[0] = $this->findInMdTree($md_tree, 'content');//content
		$temp[1] = $this->findInMdTree($md_tree, 'layout');//layout
		$temp[2] = $this->findInMdTree($md_tree, 'page');//page
		$temp[3] = $this->findInMdTree($md_tree, 'container');//container
		$temp[4] = $this->findInMdTree($md_tree, 'site');//site
		
		$md_tree = $temp;
		
		return $md_tree;
	}
	
	/**
	 * findInMdTree
	 */
	 
	public function findInMdTree($md_tree, $query) {
		
		foreach ($md_tree as $item) {
		
			if ($item['id'] == $query) return $item;
		
		}
		
	}
	
	/**
	 * filter to show only allowed items
	 */
	 
	public function filterAndAssignInfo($list) {
		
		/**
		 * retrieve template info
		 */
		 
		$templates_info = $this->retrieveTemplateInfo();
		
		/**
		 * set selected item
		 */
		
		if (!$this->GET['open']) $selected = $templates_info[$this->GET['node_group']]['default_template'];
		else $selected = $this->GET['open'];
		
		/**
		 * create filtered array
		 */
		 
		$filtered_list = array();
		
		foreach ($list as $item) {
			
			if (array_key_exists($item['parent'], $templates_info)) {
				
				//process items which are assigned in template_info or actual node has that template already selected (ie during a transition)
				if (array_key_exists($item['name'], $templates_info[$item['parent']]) || $item['name'] == $selected) {
					
					//use template info title if available
					$templates_info_item_title = trim($templates_info[$item['parent']][$item['name']]['title']);
					if ($templates_info_item_title !== '') $item['title'] = $templates_info_item_title;
					else $item['title'] = $item['name'];
				
					$filtered_list[] = $item;
				}
			} else {
				$filtered_list[] = $item;
			}
		}
		
		/**
		 * mark selected item
		 */
		
		foreach ($filtered_list as $k=>$item) {
			if ($item['name'] == $selected && $this->GET['node_group'] == $item['parent']) $filtered_list[$k]['selected'] = true;
			else $filtered_list[$k]['selected'] = false;
		}
		
		return $filtered_list;
	}
	
	/**
	 * retrieve template_info
	 */
	
	public function retrieveTemplateInfo() {
		//include always general
		require_once(ONXSHOP_DIR . "conf/node_type.php");
		//for local overwrites
		if (file_exists(ONXSHOP_PROJECT_DIR . "conf/node_type.php")) require_once(ONXSHOP_PROJECT_DIR . "conf/node_type.php");
		
		return $templates_info;
	}	
}
