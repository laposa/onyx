<?php
/** 
 * Copyright (c) 2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/common/common_node.php');

class Onxshop_Controller_Component_Teaser extends Onxshop_Controller {


	/**
	 * main action
	 */
	 
	public function mainAction() {

		$this->Node = new common_node();

		/**
		 * get input
		 */

		$target_node_id = $this->GET['target_node_id'];

		if (!is_numeric($target_node_id)) {
			msg("target_node_id parameter is not numeric", "error");
			return false;
		}

		/**
		 * load target node
		 */

		$node = $this->Node->nodeDetail($target_node_id);

		// set default link text if required
		if (trim($node['link_text']) == '') $node['link_text'] = "Find Out More";

		/**
		 * load image
		 */

		if (empty($this->GET['img_src'])) {
			$node['image'] = $this->getImage($node);
			if ($node['image']) $node['image']['src'] = "/thumbnail/210x90/" . $node['image']['src'] . "?fill=1";
		} else {
			$node['image']['src'] = $this->GET['img_src'];
			$node['image']['title'] = $node['title'];
		}

		/**
		 * override teaser text and link text if requred
		 */
		if (isset($this->GET['teaser_text']) && !empty($this->GET['teaser_text'])) $node['description'] = $this->GET['teaser_text'];
		if (isset($this->GET['link_text']) && !empty($this->GET['link_text'])) $node['link_text'] = $this->GET['link_text'];

		/**
		 * process the template
		 */

		$this->tpl->assign("NODE", $node);

		if ($node['image']['src']) $this->tpl->parse("content.image");

		return true;

	}


	/**
	 * Load Teaser image.
	 * If not available, try parent pages.
	 */
	public function getImage($node)
	{
		$image = $this->Node->getTeaserImageForNodeId($node['id']);

		if (!$image && $node['parent'] > 0) {

			$parent = $this->Node->nodeDetail($node['parent']);
			$image = $this->getImage($parent);

		}

		return $image;

	}

}
