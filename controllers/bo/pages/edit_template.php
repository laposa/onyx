<?php
/** 
 * Copyright (c) 2005-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Pages_Edit_Template extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$template['file'] = ONXSHOP_DIR . "templates/{$this->GET['template']}";
		
		$tpl = new XTemplate($template['file']);
		
		$tpl->parse('title');
		$tpl->parse('description');
		$tpl->parse('keywords');
		$tpl->parse('head');
		
		$template['title'] = $tpl->text('title');
		$template['description'] = $tpl->text('description');
		$template['keywords'] = $tpl->text('keywords');
		$template['head'] = $tpl->text('head');
		
		if (file_exists($template['file'])) {
		
			$file_content = file($template['file']);
			
			foreach ($file_content as $file_line) {
				$proc = 1;
				if (preg_match('/<\!-- BEGIN: content -->/', $file_line)) {
					$add = 1;
					$proc = 0;
				} else if (preg_match('/<\!-- END: content -->/', $file_line)) {
					$add = 0;
				}
				if ($add == 1 && $proc == 1) $template['content'] = $template['content'] . $file_line;
			}
			
		} else {
			msg("template {$template['file']} does not exists!", 'error', 1);
		}
		
		$this->tpl->assign('template', $template);

		return true;
	}
}
