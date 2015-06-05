<?php
/** 
 * Copyright (c) 2006-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Pages_Database_Import extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		//check the datafile
		if ($this->GET['data_file'] != '') {
			$data_file = ONXSHOP_PROJECT_DIR . $this->GET['data_file'];
			if (file_exists($data_file)) {
				//show controll
				$this->tpl->parse('content.import_options');
				if ($_POST['method'] == 'initial') {
					$Onxshop_Request = new Onxshop_Request("bo/database_import_initial");
				}
			} else {
				msg("Datafile $data_file does not exists", 'error');
			}
		} else {
			$this->tpl->parse('content.server_browser');
		}

		return true;
	}
}
