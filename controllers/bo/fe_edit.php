<?php
/**
 * Frontend edit controller
 *
 * Copyright (c) 2008-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Fe_edit extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		//check if we are comming from backoffice
		if ($_SERVER['REQUEST_URI'] == '/edit') {
			if (is_array($_SESSION['active_pages']) && count($_SESSION['active_pages']) > 0) {
				$node_id = $_SESSION['active_pages'][0];
				$request = translateURL("page/$node_id");
				header("Location: {$request}");
				exit;
			} else {
				header("Location: /");
			}
		}
		
		return true;
	}
}
