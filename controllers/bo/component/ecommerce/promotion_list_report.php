<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/ecommerce/promotion_list.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Promotion_List_Report extends Onxshop_Controller_Bo_Component_Ecommerce_Promotion_List {

	/**
	 * get list
	 */
	 
	public function getList() {
		
		$breakdown_period = $this->getBreakdownPeriod();
		
		$filter = array();
		$filter['created_from'] = $breakdown_period['from'];
		$filter['created_to'] = $breakdown_period['to'];
		
		$promotion_list = $this->Promotion->getAdvanceList($filter);
	
		$this->countTotal($promotion_list);
		
		return $promotion_list;
		
	}
	
	/**
	 * count total
	 */
	 
	public function countTotal($promotion_list) {
	
		if (!is_array($promotion_list)) return false;
		
		$total_count = 0;
		$total_goods_net = 0;
		$total_discount_net = 0;
		
		foreach ($promotion_list as $item) {
			$total_count = $total_count + $item['count'];
			$total_goods_net = $total_goods_net + $item['sum_goods_net'];
			$total_discount_net = $total_discount_net + $item['sum_discount_net'];
		}
		
		$this->tpl->assign('TOTAL_COUNT', $total_count);
		$this->tpl->assign('TOTAL_GOODS_NET', $total_goods_net);
		$this->tpl->assign('TOTAL_DISCOUNT_NET', $total_discount_net);
			
	}


	/**
	 * get breakdown period
	 */
	
	public function getBreakdownPeriod() {
	
		if (is_array($this->GET['reports-filter'])) {
			$breakdown_period['from'] = $this->GET['reports-filter']['from'];
			$breakdown_period['to'] = $this->GET['reports-filter']['to'];
		} else if (is_array($_SESSION['reports-filter'])) {
			$breakdown_period['from'] = $_SESSION['reports-filter']['from'];
			$breakdown_period['to'] = $_SESSION['reports-filter']['to'];
		} else {
			msg('period not selected', 'error');
			$breakdown_period['from'] = '2010-05-01';
			$breakdown_period['to'] = '2010-06-01';
		}
		
		return $breakdown_period;
	}
}

