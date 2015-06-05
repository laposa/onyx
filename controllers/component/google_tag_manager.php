<?php
/**
 * Copyright (c) 2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Google_Tag_Manager extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		if (trim($GLOBALS['onxshop_conf']['global']['google_tag_manager']) != '') {

			$this->tpl->parse('content.google_tag_manager');

		}

		return true;
	}
}
