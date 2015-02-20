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
		 * override teaser text and link text if requred
		 */
		if (isset($this->GET['teaser_text']) && !empty($this->GET['teaser_text'])) $node['description'] = $this->GET['teaser_text'];
		if (isset($this->GET['link_text']) && !empty($this->GET['link_text'])) $node['link_text'] = $this->GET['link_text'];
		
		/**
		 * load image
		 */

		if (empty($this->GET['img_src'])) {
			$node['image'] = $this->getImage($node);
			if ($node['image']) $node['image']['src'] = $node['image']['src'];
		} else {
			$node['image']['src'] = $this->GET['img_src'];
			$node['image']['title'] = $node['title'];
		}
		
		/**
		 * image size - for generating IMAGE_PATH
		 */
		 
		if (is_numeric($this->GET['image_width']) && $this->GET['image_width'] > 0) $image_width = $this->GET['image_width'];
		else $image_width = 210;
		
		if (is_numeric($this->GET['image_height']) && $this->GET['image_height'] > 0) $image_height = $this->GET['image_height'];
		else $image_height = 90;
		
		/**
		 * set image path
		 */
		 
		if ($image_width == 0) $image_path = "/image/";
		else if ($image_height > 0) $image_path = "/thumbnail/{$image_width}x{$image_height}/";
		else $image_path = "/thumbnail/{$image_width}/";
		
		$this->tpl->assign('IMAGE_PATH', $image_path);
		
		/**
		 * other resize options - generating IMAGE_RESIZE_OPTIONS
		 */
		
		$image_resize_options = array();
		
		if ($this->GET['image_method']) $image_resize_options['method'] = $this->GET['image_method'];
		if ($this->GET['image_gravity']) $image_resize_options['gravity'] = $this->GET['image_gravity'];
		if ($this->GET['image_fill']) $image_resize_options['fill'] = $this->GET['image_fill'];
		else $image_resize_options['fill'] = 1;
		
		if (count($image_resize_options) > 0) $this->tpl->assign('IMAGE_RESIZE_OPTIONS', '?'.http_build_query($image_resize_options));
		
				
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
