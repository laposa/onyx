<?php
/**
 * Copyright (c) 2009-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Google_Analytics extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		if (trim($GLOBALS['onxshop_conf']['global']['google_analytics']) != '') {

			if (defined('ONXSHOP_ENABLE_AB_TESTING') && ONXSHOP_ENABLE_AB_TESTING == true) {
				$this->tpl->assign('TEST_GROUP', $_SESSION['ab_test_group'] == 0 ? 'A': 'B');
				$this->tpl->parse('content.googleanalytics.abtesting');
			}

			$this->tpl->parse('content.googleanalytics');

		}

		return true;
	}
}
