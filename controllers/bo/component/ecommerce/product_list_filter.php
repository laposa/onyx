<?php
/**
 * Backoffice product list filter
 *
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Product_List_Filter extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		// filter
		if (isset($_POST['product-list-filter'])) $_SESSION['product-list-filter'] = $_POST['product-list-filter'];
		
		$filter = $_SESSION['product-list-filter'];
		
		$this->tpl->assign("DISABLED_selected_{$filter['disabled']}", "selected='selected'");

		return true;
	}
}
