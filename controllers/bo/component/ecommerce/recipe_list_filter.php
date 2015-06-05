<?php
/**
 *
 * Copyright (c) 2013-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Recipe_List_Filter extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		if (isset($_POST['recipe-list-filter'])) $_SESSION['bo']['recipe-list-filter'] = $_POST['recipe-list-filter'];

		$filter = $_SESSION['bo']['recipe-list-filter'];
		$this->tpl->assign('FILTER', $filter);
		
		return true;
	}
}
