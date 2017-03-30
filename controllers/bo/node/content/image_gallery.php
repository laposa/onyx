<?php
/**
 * Copyright (c) 2006-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');
require_once('models/common/common_file.php');

class Onxshop_Controller_Bo_Node_Content_Image extends Onxshop_Controller_Bo_Node_Content_Default {
	
	/**
	 * post
	 */
	 
	function post() {
	
		parent::post();
		
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
		 * local templates
		 */
		
		$this->displayLocalImageGalleryTemplates();
		
		/**
		 * template selected
		 */
		 
		$this->tpl->assign("SELECTED_template_{$this->node_data['component']['template']}", "selected='selected'");
		
	}
	
	/**
	 * displayLocalImageGalleryTemplates
	 */
	 
	function displayLocalImageGalleryTemplates() {
		
		$File = new common_file();
		$templates = $File->getFlatArrayFromFs(ONXSHOP_PROJECT_DIR . 'templates/component/image_gallery');
		
		if (is_array($templates)) {

			foreach ($templates as $file) {
				if (substr($file['title'], -5) == ".html") {
					$name = substr($file['title'], 0, -5);
					$this->tpl->assign("ITEM", array(
						"name" => $file['title'],
						"value" => $name,
						"selected" => $name == $this->node_data['component']['template'] ? 'selected="selected"' : ''
					));
					$this->tpl->parse("content.local_templates.item");
				}
			}
	
			$this->tpl->parse("content.local_templates");
			
		}
		
	}
}

