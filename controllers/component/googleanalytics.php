<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Googleanalytics extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		if (trim($GLOBALS['onxshop_conf']['global']['google_analytics']) != '') {
			$this->tpl->parse('content.googleanalytics');
		}
		
		return true;
	}
}
