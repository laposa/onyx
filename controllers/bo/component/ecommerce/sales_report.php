<?php
/** 
 * Copyright (c) 2010-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Sales_Report extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		require_once('models/ecommerce/ecommerce_order.php');
		
		$Order = new ecommerce_order();
	
		$breakdown_period = $this->getBreakdownPeriod();
		
		$product_list = $Order->getProductSalesList($breakdown_period['from'], $breakdown_period['to']);
		
		$this->renderList($product_list);
		
		return true;
	}
	 
	/**
	 * render list
	 */
	 
	public function renderList($product_list) {
	
		if (!is_array($product_list) || count($product_list) == 0) {

			$this->tpl->parse('content.empty');
			return false;
		}
					
		/**
		 * Display items and count total revenu
		 */
		
		$total_units = 0;
		$total_revenue = 0;
		
		foreach ($product_list as $item) {
			$this->tpl->assign('ITEM', $item);
			$this->tpl->parse('content.item');
			
			$total_units = $total_units + $item['count'];
			$total_revenue = $total_revenue + $item['revenue'];
		}

		$this->tpl->assign('TOTAL_UNITS', $total_units);
		$this->tpl->assign('TOTAL_REVENUE', $total_revenue);
		$this->tpl->parse('content.foot');
		
	}
	
	/**
	 * get breakdown period
	 */
	
	public function getBreakdownPeriod() {
	
		if (is_array($this->GET['reports-filter'])) {
			$breakdown_period['from'] = $this->GET['reports-filter']['from'];
			$breakdown_period['to'] = $this->GET['reports-filter']['to'];
		} else if (is_array($_SESSION['bo']['reports-filter'])) {
			$breakdown_period['from'] = $_SESSION['bo']['reports-filter']['from'];
			$breakdown_period['to'] = $_SESSION['bo']['reports-filter']['to'];
		} else {
			msg('period not selected', 'error');
			$breakdown_period['from'] = '2010-05-01';
			$breakdown_period['to'] = '2010-06-01';
		}
		
		return $breakdown_period;
	}
	

}
