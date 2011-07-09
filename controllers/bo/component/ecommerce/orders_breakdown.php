<?php
/**
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Orders_Breakdown extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * create object
		 */
		 
		require_once('models/ecommerce/ecommerce_order.php');
		$Order = new ecommerce_order();
		
		/**
		 * input date
		 */

		if (is_array($this->GET['reports-filter'])) {
			
			$breakdown['from'] = $this->GET['reports-filter']['from'];
			$breakdown['to'] = $this->GET['reports-filter']['to'];
			
		} else if (is_array($_SESSION['reports-filter'])) {
			$breakdown['from'] = $_SESSION['reports-filter']['from'];
			$breakdown['to'] = $_SESSION['reports-filter']['to'];
		} else {
			//get actual date
			$this_year = date('Y');
			$this_month = date('m');
			
			//get last month
			$previous_month = $this_month - 1;
			if ($previous_month < 1) {
					$previous_month = "12";
					$year_previous_month = $this_year - 1;
			} else {
				$year_previous_month = $this_year;
			}
			if ($previous_month < 10) $previous_month = "0$previous_month";
			
			//format
			$breakdown['from'] = "$year_previous_month-$previous_month-01";
			$breakdown['to'] = "$this_year-$this_month-01";
		}
		
		$this->tpl->assign("SELECTED_$time_frame", "selected='selected'");
		$this->tpl->assign("TIME_FRAME", $time_frame);
		
		/**
		 * get data
		 */
		 
		$breakdown_data = $Order->getBreakdown($breakdown['from'], $breakdown['to']);
		
		/**
		 * display and custom calculations
		 */
		 
		if (is_array($breakdown_data['goods']['type'])) {
		
			foreach ($breakdown_data['goods']['type'] as $type_name=>$v) {
		
				$v['name'] = $type_name;
				$v['net'] = sprintf("%0.2f", $v['net']);
				$v['vat'] = sprintf("%0.2f", $v['vat']);
				$this->tpl->assign('GOODS_TYPE', $v);
				$this->tpl->parse('content.result.type');
			}
			
			
			$breakdown_data['goods']['total']['net'] = sprintf("%0.2f", $breakdown_data['goods']['total']['net']);
			$breakdown_data['goods']['total']['vat'] = sprintf("%0.2f", $breakdown_data['goods']['total']['vat']);
			
			$breakdown_data['goods']['charged']['net'] = sprintf("%0.2f", $breakdown_data['goods']['charged']['net']);
			$breakdown_data['goods']['charged']['vat'] = sprintf("%0.2f", $breakdown_data['goods']['charged']['vat']);
			
			$breakdown_data['charged']['net']['total'] = sprintf("%0.2f", $breakdown_data['goods']['charged']['net'] + $breakdown_data['delivery']['net'] + $breakdown_data['delivery']['vat_exempt']);
			$breakdown_data['charged']['vat']['total'] = sprintf("%0.2f", $breakdown_data['goods']['charged']['vat'] + $breakdown_data['delivery']['vat']);
			$breakdown_data['charged']['sum']['goods'] = sprintf("%0.2f", $breakdown_data['goods']['charged']['net'] + $breakdown_data['goods']['charged']['vat']);
			$breakdown_data['charged']['sum']['delivery'] = sprintf("%0.2f", $breakdown_data['delivery']['net'] + $breakdown_data['delivery']['vat'] + $breakdown_data['delivery']['vat_exempt']);
			$breakdown_data['charged']['sum']['total'] = $breakdown_data['charged']['sum']['goods'] + $breakdown_data['charged']['sum']['delivery'];
			
			$this->tpl->assign('DELIVERY', $breakdown_data['delivery']);
			$this->tpl->assign('GOODS', $breakdown_data['goods']);
			$this->tpl->assign('CHARGED', $breakdown_data['charged']);
			$this->tpl->assign('CHECK', $breakdown_data['check']);
			
			$this->tpl->parse('content.result');
		} else {
			msg("No data available");
		}
		
		$this->tpl->assign('BREAKDOWN', $breakdown);

		return true;
	}
}		
