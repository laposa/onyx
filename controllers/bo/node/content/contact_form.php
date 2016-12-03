<?php
/** 
 * Copyright (c) 2006-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Contact_form extends Onxshop_Controller_Bo_Node_Content_Default {
	
	/**
	 * post action
	 */
	 
	function post() {
		
		parent::post();
		
		//only for including of configuration
		require_once('models/common/common_email.php');
		$EmailForm = new common_email();
		$this->tpl->assign('EMAIL_FORM_CONF', $EmailForm->conf);

		require_once('models/common/common_file.php');
		$File = new common_file();
		
		/**
		 * set default email template
		 */
		
		if (empty($this->node_data['component']['node_controller'])) $this->node_data['component']['node_controller'] = $this->Node->conf['contact_form_default_template'];
		
		/**
		 * contact form template directory name
		 */
		 
		$directory = "templates/component/contact_form/";
		$this->tpl->assign('DIRECTORY', $directory);
		
		// show warning if old _contact_form directory is found
		$old_directory = 'templates/component/_contact_form/';
		if (file_exists(ONXSHOP_PROJECT_DIR . $old_directory)) msg("Found deprecated folder name in your installation. Please contact your developers and ask them to rename $old_directory to $directory", 'error');
		
		/**
		 * list templates
		 */
		 
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

		$this->tpl->assign("SPAM_PROTECTION", array(
			'captcha_image' => ($this->node_data['component']['spam_protection'] == 'captcha_image' ? 'selected="selected"' : ''),
			'captcha_text_js' => ($this->node_data['component']['spam_protection'] == 'captcha_text_js' ? 'selected="selected"' : '')
		));

	}
}
