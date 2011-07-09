<?php
/**
 * Order detail controller
 *
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Pages_Ecommerce_Order_Detail extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * check
		 */
		 
		if (is_numeric($this->GET['id'])) $order_id = $this->GET['id'];
		else return false;
		
		/**
		 * initialise
		 */
		 
		require_once('models/ecommerce/ecommerce_order.php');
		$Order = new ecommerce_order();
		
		/**
		 * save
		 */
		 
		if (isset($_POST['save'])) {
			
			$order_data = $Order->getDetail($order_id);
		
			if ($order_data['status'] != $_POST['order']['status']) {
				$Order->setStatus($order_id, $_POST['order']['status']);
				$order_data['status'] = $_POST['order']['status'];
			}
		
			$order_data['note_backoffice'] = $_POST['order']['note_backoffice'];
		
			if ($Order->updateOrder($order_data)) {
				$order_data = $Order->getOrder($order_id);
				//msg('Updated');
			}
		}
		
		/**
		 * get full detail (including relations)
		 */
		 
		$full_order_data = $Order->getOrder($order_id);
		
		
		// status
		$status = $Order->conf['status'];
		
		// parse select box
		foreach ($status as $key=>$s) {
			$s1['id'] = $key;
			$s1['name'] = $s;
			if ($s1['id'] == $full_order_data['status']) {
				$s1['selected'] = 'selected="selected"';
			} else {
				$s1['selected'] = '';
			}
			$this->tpl->assign('STATUS', $s1);
			$this->tpl->parse('content.status');
		}
		
		// parse log
		foreach ($full_order_data['log'] as $log) {
			$log['name'] = $status[$log['status']];
			$this->tpl->assign('STATUS', $log);
			$this->tpl->parse('content.log');
		}
		
		if ($full_order_data['note_customer'] == '') $full_order_data['note_customer'] = "n/a.";
		$this->tpl->assign('ORDER', $full_order_data);
		
		/**
		 * ACL
		 */
		 
		if (!preg_match("/-warehouse$/", $_SESSION['authentication']['username'])) {
			$this->tpl->parse('content.customer_detail');
			$this->tpl->parse('content.accounting_detail');
		}

		return true;
	}
}
