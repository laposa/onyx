<?php
/** 
 *
 * Copyright (c) 2009-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/common/common_node.php');
require_once('models/common/common_image.php');
require_once('models/common/common_node_taxonomy.php');

class Onxshop_Controller_Bo_Component_Node_Duplicate extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		if (is_numeric($this->GET['id'])) $original_node_id = $this->GET['id'];
		else return false;
		
		$this->Node = new common_node();
		$this->Image = new common_image();
		$this->Image = new common_image();
		$this->Taxonomy = new common_node_taxonomy();
		
		$new_node_id = $this->duplicateNode($original_node_id);

		if ($new_node_id) {
			msg("Content successfully duplicated.");

			$_Onxshop_Request = new Onxshop_Request("node~id=$new_node_id~");
			$this->tpl->assign('NODE_DETAIL', $_Onxshop_Request->getContent());
		}

		return $new_node_id > 0;
	}


	/**
	 * recursivelly duplicate node and its contens
	 */

	protected function duplicateNode($original_node_id, $new_parent_id = false)
	{
		// read original node
		$original_node_data = $this->Node->detail($original_node_id);
		
		// copy and modify
		$new_node_data = $original_node_data;
		$new_node_data['title'] = "{$new_node_data['title']} (copy)";
		$new_node_data['created'] = $new_node_data['modified'] = date('c');
		$new_node_data['customer_id'] = (int) Onxshop_Bo_Authentication::getInstance()->getUserId();
		if ($new_node_data['uri_title'] != '') $new_node_data['uri_title'] = "{$new_node_data['uri_title']}-copy";
		if ($new_parent_id > 0) $new_node_data['parent'] = $new_parent_id;
		else {
			//top level element can be forced to be unpublished via common_node.conf option
			if ($this->Node->conf['unpublish_on_duplicate']) $new_node_data['publish'] = 0;
		}
		unset($new_node_data['id']);

		// insert as new
		$new_node_id = $this->Node->nodeInsert($new_node_data);
		if (!is_numeric($new_node_id)) {
			msg("node_duplicate: Cannot create copy of node ID $original_node_id", 'error');
			return false;
		}

		// read related images
		$original_images = $this->Image->listing("node_id = $original_node_id");

		// duplicate images
		if (is_array($original_images)) {
			foreach ($original_images as $image) {
				$new_image = $image;
				$new_image['node_id'] = $new_node_id;
				$new_image['modified'] = date('c');
				$new_image['customer_id'] = (int) Onxshop_Bo_Authentication::getInstance()->getUserId();
				unset($new_image['id']);
				$image_id = $this->Image->insert($new_image);
			}
		}

		// read taxonomy relatoins
		$original_categories = $this->Taxonomy->listing("node_id = $original_node_id");

		// duplicate taxonomy relations
		if (is_array($original_categories)) {
			foreach ($original_categories as $category) {
				$new_category = $category;
				$new_category['node_id'] = $new_node_id;
				unset($new_category['id']);
				$category_id = $this->Taxonomy->insert($new_category);
			}
		}

		// read and duplicate nested nodes, but skip page nodes
		$nested_nodes = $this->Node->listing("parent = $original_node_id");
		if (is_array($nested_nodes)) {
			foreach ($nested_nodes as $nested_node) {
				if ($nested_node['node_group'] != 'page')
					$this->duplicateNode($nested_node['id'], $new_node_id);
			}
		}

		return $new_node_id;
	}

}
