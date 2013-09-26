<?php
/**
 * Copyright (c) 2006-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * TODO: rename to image_gallery
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Picture extends Onxshop_Controller_Node_Content_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/* we need to include config*/
		require_once('models/common/common_image.php');
		$common_image_conf = common_image::initConfiguration();
		
		require_once('models/common/common_node.php');
		
		$Node = new common_node();
		
		$node_data = $Node->nodeDetail($this->GET['id']);
		
		if ($node_data['component']['template'] == '') $node_data['component']['template'] = 'single';
		
		/**
		 * set width
		 */
		 
		if (is_numeric($node_data['component']['main_image_width'])) {
			$image_width = $node_data['component']['main_image_width'];
		} else {
			$image_width = 0;
		}
		
		/**
		 * check constrain and set appropriate height
		 */
		 
		switch ($node_data['component']['main_image_constrain']) {
			
			case '1-1':
				$image_height = $image_width;
			break;
			
			case '0':
			default:
				$image_height = 0;
			break;
		}
		
		/**
		 * what template
		 */
		 
		if ($node_data['component']['template'] == 'plain') {
		
			$image_controller = 'component/image_gallery';
		
		} else if (getTemplateDir('component/image_gallery/' . $node_data['component']['template'] . '.html') != '') {
		
			$image_controller = 'component/image_gallery/' . $node_data['component']['template'];
		
		} else {
		
			// i.e. list
			$image_controller = 'component/image';
		
		}
		
		/**
		 * check cycle link_to_node_id
		 */
		 
		if ($node_data['component']['cycle']['link_to_node_id']) {
			$cycle['link_to_node_id'] = $node_data['component']['cycle']['link_to_node_id'];
		} else {
			$cycle['link_to_node_id'] = '';
		}
		
		/**
		 * timeout for slideshow
		 */
		 
		if ($node_data['component']['cycle']['fx']) $cycle['fx'] = $node_data['component']['cycle']['fx'];
		else $cycle['fx'] = $common_image_conf['cycle_fx'];
		if ($node_data['component']['cycle']['easing']) $cycle['easing'] = $node_data['component']['cycle']['easing'];
		else $cycle['easing'] = $common_image_conf['cycle_easing'];
		if (is_numeric($node_data['component']['cycle']['speed'])) $cycle['speed'] = $node_data['component']['cycle']['speed'];
		else $cycle['speed'] = $common_image_conf['cycle_speed'];
		if (is_numeric($node_data['component']['cycle']['timeout'])) $cycle['timeout'] = $node_data['component']['cycle']['timeout'];
		else $cycle['timeout'] = $common_image_conf['cycle_timeout'];
		
		/**
		 * disable limit
		 */
		
		$image_limit = '';
		
		/**
		 * call controller
		 */
		 
		$Onxshop_Request = new Onxshop_Request("{$image_controller}~relation=node:role=main:width={$image_width}:height={$image_height}:node_id={$node_data['id']}:limit={$image_limit}:cycle_fx={$cycle['fx']}:cycle_easing={$cycle['easing']}:cycle_timeout={$cycle['timeout']}:cycle_speed={$cycle['speed']}:cycle_link_to_node_id={$cycle['link_to_node_id']}~");
		$this->tpl->assign("CONTENT", $Onxshop_Request->getContent());
		
		$this->tpl->assign('NODE', $node_data);
		$this->tpl->assign('IMAGE_CONF', $common_image_conf);
		
		/**
		 * display title
		 */
		 
		$this->displayTitle($node_data);

		return true;
	}
}
