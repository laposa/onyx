<?php
/**
 *
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Pages_Ecommerce_Customers extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * conditional display of different buttons
		 */
		 
		if ($_SESSION['customer-filter-selected_group_id'] > 0) $this->tpl->parse('content.modify_group');
		else $this->tpl->parse('content.create_new_group');
				
		return true;
	}
}
