<?php
/** 
 * Copyright (c) 2012-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/common/common_node.php');
require_once('models/common/common_image.php');

class Onxshop_Controller_Component_Social_Network_Share extends Onxshop_Controller {

	// models
	public $Node;
	public $Image;

	// key
	public $node_id;

	// data structures
	public $node;
	public $image;

	/**
	 * main action
	 */

	public function mainAction() {

		$this->initModels();
		$this->node_id = $this->getNodeId();
		$this->node = $this->getNode($this->node_id);
		$this->image = $this->getImage($this->node_id);

		$share_uri = "http://".$_SERVER['HTTP_HOST']."/page/{$this->node_id}";

		$this->tpl->assign('SHARE_URI', $share_uri);
		$this->tpl->assign('IMAGE', $this->image);
		$this->tpl->assign('NODE', $this->node);

		return true;
		
	}

	protected function initModels() {

		$this->Node = new common_node();
		$this->Image = new common_image();

	}

	protected function getNodeId() {

		if (is_numeric($this->GET['node_id'])) return $this->GET['node_id'];
		return 5; //homepage

	}

	protected function getNode($node_id) {

		$node_data = $this->Node->nodeDetail($node_id);
		if ($node_data['page_title'] == '') $node_data['page_title'] = $node_data['title'];
		return $node_data;
	}

	protected function getImage($node_id) {

		$image_list = $this->Image->listFiles($node_id);
		if (is_array($image_list) && count($image_list) > 0) return $image_list[0];
		return array('src' => 'var/files/favicon.ico');

	}
}
