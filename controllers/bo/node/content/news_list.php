<?php
/**
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Content_News_List extends Onxshop_Controller_Bo_Node_Default {

	/**
	 * pre action
	 */

	function pre() {
	
		if (!(is_numeric($_POST['node']['component']['limit']) && $_POST['node']['component']['limit'] > 0)) $_POST['node']['component']['limit'] = 5;
		if ($_POST['node']['component']['pagination'] == 'on') $_POST['node']['component']['pagination'] = 1;
		else $_POST['node']['component']['pagination'] = 0;
		if ($_POST['node']['component']['image'] == 'on') $_POST['node']['component']['image'] = 1;
		else $_POST['node']['component']['image'] = 0;
		if ($_POST['node']['component']['display_title'] == 'on') $_POST['node']['component']['display_title'] = 1;
		else $_POST['node']['component']['display_title'] = 0;

	}
	
	/**
	 * post action
	 */
	 
	function post() {
	
		/* we need to include config, can be removed when we initialize all conf on beggining */
		require_once('models/common/common_image.php');
		$common_image_conf = common_image::initConfiguration();
		$this->tpl->assign('IMAGE_CONF', $common_image_conf);
		
		/**
		 * main image width
		 */
		
		if ($this->node_data['component']['image_width'] == 0) {
			$this->tpl->assign("SELECTED_image_width_original", "selected='selected'");
			$this->node_data['component']['image_width'] = 125;
		} else {
			$this->tpl->assign("SELECTED_image_width_custom", "selected='selected'");
		}
		
		/**
		 * image ratio constrain
		 */
		 
		$this->tpl->assign("SELECTED_image_constrain_{$this->node_data['component']['image_constrain']}", "selected='selected'");
		
		/**
		 * other options
		 */
	
		$this->node_data['component']['pagination']        = ($this->node_data['component']['pagination']) ? 'checked="checked"'      : '';
		$this->node_data['component']['image']        = ($this->node_data['component']['image']) ? 'checked="checked"'      : '';
		$this->node_data['component']['display_title']        = ($this->node_data['component']['display_title']) ? 'checked="checked"'      : '';
		
		/**
		 * template selected
		 */
		 
		$this->tpl->assign("SELECTED_template_{$this->node_data['component']['template']}", "selected='selected'");
		
	}
}

