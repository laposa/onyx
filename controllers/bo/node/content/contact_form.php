<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Content_Contact_form extends Onxshop_Controller_Bo_Node_Default {
	
	/**
	 * post action
	 */
	 
	function post() {
		//only for including of configuration
		require_once('models/common/common_email_form.php');
		$EmailForm = new common_email_form();
		$this->tpl->assign('EMAIL_FORM_CONF', $EmailForm->conf);

		require_once('models/common/common_file.php');
		$File = new common_file();

		$directory = "templates/component/_contact_form/";
		$this->tpl->assign('DIRECTORY', $directory);
		$templates = $File->getFlatArrayFromFsJoin($directory);
		$templates = array_reverse($templates);

		if (is_array($templates)) {
			foreach ($templates as $template) {
				$template['name'] = str_replace('.html', '', $template['name']);
						
				if ($template['name'] == $this->node_data['component']['node_controller']) $template['selected'] = "selected='selected'";
				else $template['selected'] = '';
	
				$template['title'] = $templates_info[$this->GET['node_group']][$template['name']]['title'];
				if ($template['title'] == '') $template['title'] = $template['name'];
				$this->tpl->assign('LAYOUT_TEMPLATE', $template);
				$this->tpl->parse('content.templateitem');
			}
		}
	}
}
