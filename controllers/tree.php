<?php
/**
 * Copyright (c) 2010-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * this is really node_tree as it depends on common_node
 *
 */

class Onxshop_Controller_Tree extends Onxshop_Controller {

	public $full_path = array();
	
	/**
	 * main action
	 */
	
	public function mainAction() {
		
		return $this->standardAction();
		
	}
	
	/**
	 * standard action
	 */
	 
	public function standardAction($node_id = null, $publish = 1, $max_display_level = 0,  $expand_all = 0, $node_group = 'page') {
		
		$list = $this->getList($publish, $node_group);
		
		if (count($list) > 0) {
		
			$md_tree = $this->buildTree($list, $node_id);
			
			if (count($md_tree) > 0) {
				$end_result = $this->nestedListFromArray($md_tree, $publish, $max_display_level, $expand_all);
		
				$this->tpl->assign('END_RESULT_GROUPS', $end_result);
			}
		}
		
		return true;
	}
	
	/**
	 * get list
	 */
	 
	public function getList($publish = 1, $node_group = 'page') {
		
		require_once('models/common/common_node.php');
		
		$Node = new common_node();
		$list = $Node->getTree($publish, $node_group);

		return $list;
	}
	

	/**
	 * build tree from a 2D array
	 */
	 
	function buildTree($nodes, $id = null, $level = 1) {

		$tree = array();

		if(is_array($nodes)) {
		
			foreach($nodes as $node) {
			
				// safety check
				if ($node['parent'] === $node['id']) {
					msg("Infinite loop in tree.buildTree (id={$node['id']}, parent={$node['parent']}", 'error');
					return false;
				}
				
				if ($node["parent"] == $id) array_push($tree, $node);
			}

			for($x = 0; $x < count($tree); $x++) {
				
				$tree[$x]["level"] = $level;
				$tree[$x]["children"] = $this->buildTree($nodes, $tree[$x]["id"], $level + 1);

			}

			return $tree;

		}

	}
	
	/**
	 * render list from array
	 *
	 */
	 
	function nestedListFromArray($tree, $publish = 1, $max_display_level = 0, $expand_all = 0) {

		/**
		 * check input data type
		 */
		 
		if (!is_array($tree)) {
		
			msg("listFromArray: tree is not an array", 'error');
			return false;
		
		} else if (count($tree) == 0) {
		
			return true;
		
		}
		
		/**
		 * filter by display permissions
		 */
		 
		$tree = $this->processPermission($tree);
				
		/**
		 * set variables
		 */
		
		$i = 0;
		$count = count($tree);
		
		/**
		 * process all items in this level
		 */
		
		foreach ($tree as $item) {
		
			/**
			 * stop if are not expanding all and not selected item
			 */
			if ($expand_all == 0) {
				if ($item['parent'] != $this->GET['open'] && $item['parent'] != $this->GET['id'] && !in_array($item['parent'], $this->full_path)) return false;
			}
			
			/**
			 * stop at max_display_level, but only when a level limit is set
			 */
			 
			if ($max_display_level > 0) {
				if ($item['level'] > $max_display_level) return false;
			}
			
			
			
			/**
			 * initialise css_class
			 */
			 
			$item['css_class'] = '';
			
			/**
			 * assign first, middle, last CSS class
			 */
			 
			if ($i == 0) $item['css_class'] = 'first';
			else if ($i == ($count - 1)) $item['css_class'] = 'last';
			else $item['css_class'] = 'middle';
			
			/**
			 * processing children
			 */
			
			if (count($item['children']) > 0) {
			
				$item['css_class'] = $item['css_class'] . ' has_child';
				
				$item['subcontent'] = $this->nestedListFromArray($item['children'], $publish, $max_display_level, $expand_all);
				
			} else {
			
				$item['subcontent'] = '';
			
			}
			
			if ($item_parsed = $this->parseItem($item)) {
				
				$end_result_items .= $item_parsed;
				
			}
			
			$i++;

		}
		
		$this->tpl->assign('END_RESULT_ITEMS', $end_result_items);
		
		$group_parsed = $this->parseGroup();
		//echo $group_parsed;
		
		return $group_parsed;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param array $item
	 * @return text html
	 */
	 
	function parseItem($item) {
	
		/**
		 * use description for HTML title attribute if available
		 * or set HTML title as item name if not available
		 */
		 
		if ($item['description'] != '') $item['title'] = $item['description'];
		else if ($item['title'] == '') $item['title'] = $item['name'];
		
		/**
		 * set open and active class
		 */
		 
		if (in_array($item['id'], $this->full_path)) {
			$item['css_class'] = $item['css_class'] . " open";
			if ($item['id'] == $_SESSION['active_pages'][0]) $item['css_class'] = $item['css_class'] . " active";
		}
		
		/**
		 * Add open class to last_open_folder
		 * ? is it a hack for server_browser??
		 */
		 
		if ($_SESSION['server_browser_last_open_folder'] != "") {
			$preg = str_replace("/", "\/", quotemeta($item['id']));
			if (preg_match("/{$preg}/", $_SESSION['server_browser_last_open_folder'])) {
				$item['css_class'] = $item['css_class'] . " open";
				if (preg_match("/{$preg}$/", $_SESSION['server_browser_last_open_folder'])) {
					$item['css_class'] = $item['css_class'] . " active";
				}
			}
		}
		
		/**
		 * add publish, no_publish class if we showing all items
		 */
		
		if (isset($this->GET['publish'])) {
			if ($item['publish'] == 0) $item['css_class'] = $item['css_class'] . " onxshop_nopublish";
		}
		

		/**
		 * assign to template
		 */
		 
		$item['children'] = null; //dont assign children to save memory
		$this->tpl->assign('ITEM',$item);
		
		/**
		 * other specific things, should be moved to separate controllers
		 */
		 
		if ($this->GET['display_teaser'] && trim($item['teaser']) != '') {
			$this->tpl->parse('content.group.item.link.teaser');
		}
		
		/**
		 * parse no link block if appropriate
		 */
		
		if ($item['display_in_menu'] == 2 || $item['node_group'] == 'container') {
			$this->tpl->parse('content.group.item.nolink');
		} else {
			$this->tpl->parse('content.group.item.link');
		}
		
		/**
		 * parse item, get as text and reset template block
		 */
		 
		$this->tpl->parse('content.group.item');
		$text = $this->tpl->text('content.group.item');
		$this->tpl->reset('content.group.item');
		
		return $text;
	}
	
	/**
	 * parse group, get as text and reset template block
	 *
	 * @return text html
	 */
	 
	function parseGroup() {	
		
		$this->tpl->parse('content.group');
		$text = $this->tpl->text('content.group'); 
		$this->tpl->reset('content.group');
		
		return $text;
	}
	
	/**
	 * process persmission
	 */
	 
	function processPermission($tree) {
	
		$filtered_tree = array();
		
		foreach ($tree as $item) {
		
			/**
			 * display_permission
			 */
			
			if (is_numeric($item['display_permission'])) {
				
				//common_node should be included, so we can call it's static method
				if (common_node::checkDisplayPermission($item)) $filtered_tree[] = $item;
				
			} else {
			
				//it's not a node with display_permission (could be a file)
				$filtered_tree[] = $item;
				
			}
		
		}
		
		return $filtered_tree;
	}
}


