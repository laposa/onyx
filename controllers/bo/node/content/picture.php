<?php
/**
 * Copyright (c) 2006-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Content_Picture extends Onxshop_Controller_Bo_Node_Default {
	
	/**
	 * post
	 */
	 
	function post() {
	
		/* we need to include config, can be removed when we initialize all conf on beggining */
		require_once('models/common/common_image.php');
		$common_image_conf = common_image::initConfiguration();

		$this->tpl->assign('IMAGE_CONF', $common_image_conf);
		
		//TODO?: images size can be extracted from COMMON_IMAGE_THUMBNAIL
		
		/**
		 * cycle gallery options
		 */
		 
		if ($this->node_data['component']['template'] == 'cycle') {
			
			if (!$this->node_data['component']['cycle']['fx']) $this->node_data['component']['cycle']['fx'] = $common_image_conf['common_image']['cycle_fx'];
			if (!$this->node_data['component']['cycle']['easing']) $this->node_data['component']['cycle']['easing'] = $common_image_conf['common_image']['cycle_easing'];
			if (!is_numeric($this->node_data['component']['cycle']['timeout']) || $this->node_data['component']['cycle']['timeout'] < 0) $this->node_data['component']['cycle']['timeout'] = $common_image_conf['common_image']['cycle_timeout'];
			if (!is_numeric($this->node_data['component']['cycle']['speed']) || $this->node_data['component']['cycle']['speed'] < 0) $this->node_data['component']['cycle']['speed'] = $common_image_conf['common_image']['cycle_speed'];
		
			
			$this->tpl->assign("SELECTED_cycle_fx_{$this->node_data['component']['cycle']['fx']}", "selected='selected'");
			$this->tpl->assign("SELECTED_cycle_easing_{$this->node_data['component']['cycle']['easing']}", "selected='selected'");
			
			//must assign NODE before parsing
			$this->tpl->assign("NODE", $this->node_data);
			
			$this->tpl->parse('content.cycle_options');
		}
		
		/**
		 * main image width
		 */
		
		if ($this->node_data['component']['main_image_width'] == 0) {
			$this->tpl->assign("SELECTED_main_image_width_original", "selected='selected'");
			
			$Image = new common_image();
			$image_list = $Image->listFiles($this->node_data['id']);
			
			$image_width = 9999;
			
			foreach ($image_list as $item) {
				if ($item['imagesize']['width'] < $image_width) $image_width = ($item['imagesize']['width'] - $item['imagesize']['width'] % 5);
			}
			
			if ($image_width == 9999) $image_width = 800;
			
			$this->node_data['component']['main_image_width'] = $image_width;
			
		} else {
			$this->tpl->assign("SELECTED_main_image_width_custom", "selected='selected'");
		}
		
		/**
		 * image ratio constrain
		 */
		 
		$this->tpl->assign("SELECTED_main_image_constrain_{$this->node_data['component']['main_image_constrain']}", "selected='selected'");

		/**
		 * template selected
		 */
		 
		$this->tpl->assign("SELECTED_template_{$this->node_data['component']['template']}", "selected='selected'");
		
		/**
		 * fill option
		 */
		 
		$this->tpl->assign("SELECTED_fill_{$this->node_data['component']['fill']}", "selected='selected'");
	}
}

