<?php
/** 
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Pages_Advanced extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		/**
		 * Manage Menu
		 */
		if (preg_match('/backoffice/', $_SERVER['REQUEST_URI'])) {
			$active_array = explode("/", $_SERVER['REQUEST_URI']);
			$active = preg_replace("/\?.*$/", "", $active_array[3]);
		} else {
			$active = 'configuration';
		}
		
		$this->tpl->assign("ACTIVE_{$active}", 'active ui-tabs-selected ui-state-active');

		return true;
	}
}
