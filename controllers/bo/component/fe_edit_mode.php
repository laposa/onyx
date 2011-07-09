<?php
/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Fe_edit_Mode extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		if ($_POST['fe_edit_mode']) {
			$mode = $_POST['fe_edit_mode'];
		} else if ($_SESSION['fe_edit_mode']) {
			$mode = $_SESSION['fe_edit_mode'];
		} else {
			$mode = 'preview';
		}
				
		$_SESSION['fe_edit_mode'] = $mode;

		$this->tpl->assign("SELECTED_$mode", 'selected="selected"');
		$this->tpl->parse("content.fe_edit_mode_$mode");
		
		return true;
	}
}

