<?php
/** 
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/common/common_node.php');
require_once('models/common/common_image.php');

class Onxshop_Controller_Bo_Component_Node_Duplicate extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		if (is_numeric($this->GET['id'])) $original_node_id = $this->GET['id'];
		else return false;
		
		$this->Node = new common_node();
		$this->Image = new common_image();
		
		$new_node_id = $this->duplicateNode($original_node_id);

		if ($new_node_id) {
			msg("Content successfully duplicated.");

			$_nSite = new nSite("node~id=$new_node_id~");
			$this->tpl->assign('NODE_DETAIL', $_nSite->getContent());
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
		$new_node_data = $original_node_data;
		$new_node_data['title'] = "{$new_node_data['title']} (copy)";
		if ($new_parent_id > 0) $new_node_data['parent'] = $new_parent_id;
		unset($new_node_data['id']);

		// insert as new
		$new_node_id = $this->Node->insert($new_node_data);
		if (!is_numeric($new_node_id)) {
			msg('node_duplicate: Cannot create node', 'error');
			return false;
		}

		// read related images
		$original_images = $this->Image->listing("node_id = $original_node_id");

		// duplicate images
		if (is_array($original_images)) {
			foreach ($original_images as $image) {
				$new_image = $image;
				$new_image['node_id'] = $new_node_id;
				unset($new_image['id']);
				$image_id = $this->Image->insert($new_image);
			}
		}

		// read and duplicate nested nodes
		$nested_nodes = $this->Node->listing("parent = $original_node_id");
		if (is_array($nested_nodes)) {
			foreach ($nested_nodes as $nested_node) {
				$this->duplicateNode($nested_node['id'], $new_node_id);
			}
		}

		return $new_node_id;
	}

}
