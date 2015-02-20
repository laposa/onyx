<?php
/** 
 * Copyright (c) 2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');
require_once('models/common/common_node.php');

class Onxshop_Controller_Node_Content_Page_List extends Onxshop_Controller_Node_Content_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$this->Node = new common_node();

		$node_data = $this->Node->nodeDetail($this->GET['id']);
		
		/**
		 * image size
		 */
		
		/**
		 * image size: set width
		 */
		 
		if (is_numeric($node_data['component']['image_width'])) {
			$image_width = $node_data['component']['image_width'];
		} else {
			$node_data['component']['image_width'] = 0;
			$image_width = 0;
		}
		
		/**
		 * image size: check constrain and set appropriate height
		 */
		 
		switch ($node_data['component']['image_constrain']) {
			
			case '1-1':
				$image_height = $image_width;
			break;
			
			default:
			case '2_39-1': // 2.39:1
				$image_height = (int)($image_width / 2.39 * 1);
			break;
			
			case '16-9':
				$image_height = (int)($image_width / 16 * 9);
			break;
			
			case '4-3':
				$image_height = (int)($image_width / 16 * 9);
			break;
			
			case '0':
				$image_height = 0;
			break;
			
		}
		
		/**
		 * image size: fill cropping option
		 * 
		 */
		 
		if (is_numeric($node_data['component']['image_fill'])) $image_fill = $node_data['component']['image_fill'];
		else $image_fill = 1;

		/**
		 * call controller
		 */

		$node_ids = explode(",", trim($node_data['component']['node_ids']));
		$content = '';

		foreach ($node_ids as $node_id) {

			if (is_numeric($node_id)) {
				$_Onxshop_Request = new Onxshop_Request("component/teaser~target_node_id={$node_id}:image_width=$image_width:image_height=$image_height:image_fill=$image_fill~");
				$content .= $_Onxshop_Request->getContent();
			}

		}

		$this->tpl->assign('CONTENT', $content);
		$this->tpl->assign('NODE', $node_data);

		if ($node_data['display_title'])  $this->tpl->parse('content.title');

		return true;
	}


}
