<?php
/**
 *
 * Copyright (c) 2011-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Client_Customer_Search extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * initialise groups
		 */
		 
		require_once('models/client/client_group.php');
		$ClientGroup = new client_group();
		
		/**
		 * Store submited data to the SESSION
		 */
		
		if (isset($_POST['customer-filter'])) $_SESSION['customer-filter'] = $_POST['customer-filter'];
		
		if (!is_array($_SESSION['customer-filter'])) {
			$_SESSION['customer-filter'] = array();
			$_SESSION['customer-filter']['invoice_status'] = 0;
			$_SESSION['customer-filter']['account_type'] = -1;
		}
		
		//save selected group for use in filter update
		$_SESSION['customer-filter-selected_group_id'] = $_SESSION['customer-filter']['group_id'];
		
		/**
		 * list groups
		 */
		 
		$list = $ClientGroup->listGroups();
		
		//select boxes
		$this->tpl->assign("SELECTED_group_{$_SESSION['customer-filter']['group_id']}", 'selected="selected"');
		$this->tpl->assign("SELECTED_account_type_{$_SESSION['customer-filter']['account_type']}", 'selected="selected"');
		
		foreach ($list as $item) {
			
			$this->tpl->assign('ITEM', $item);
			
			if ($item['id'] == $_SESSION['customer-filter']['group_id']) $this->tpl->assign('SELECTED', 'selected="selected"');
			else $this->tpl->assign('SELECTED', '');
			
			$this->tpl->parse('content.item');
		}
		 
		return true;
	}
}
