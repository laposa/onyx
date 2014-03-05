<?php
/**
 * Stock control
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Stock extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * get filter
		 */
		 
		if ($_POST['product-list-filter']) $filter = $_POST['product-list-filter'];
		else if (is_array($_SESSION['product-list-filter'])) $filter = $_SESSION['product-list-filter'];
		else $filter = array();
		
		/**
		 * Initialize order object
		 */
		require_once('models/ecommerce/ecommerce_product.php');
		
		$Product = new ecommerce_product();
		
		/**
		 * Get order list
		 */
		$product_list = $Product->getFilteredProductList($filter);
		
		
		if (count($product_list) > 0) {
			
			/**
			 * Initialize pagination variables
			 */
			
			if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
			else $from = 0;
			if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
			else $per_page = 25;
			
			
			$limit = "$from,$per_page";
			
			
			/**
			 * Display pagination
			 */
			
			//$link = "/page/" . $_SESSION['active_pages'][0];
			$count = count($product_list);
			
			$_Onxshop_Request = new Onxshop_Request("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count~");
			$this->tpl->assign('PAGINATION', $_Onxshop_Request->getContent());
			
		
			
			/**
			 * Display items
			 * Implemented pagination
			 */
		
			$product_list = array_reverse($product_list);
		
			foreach ($product_list as $i=>$item) {
				
				if ($i >= $from  && $i < ($from + $per_page) ) {
					$even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';
					$item['even_odd'] = $even_odd;
				
					$item['disabled'] = ($item['publish']) ? '' : 'disabled';
			
					$this->tpl->assign('ITEM', $item);
					$this->tpl->parse('content.item');
				}
			}
		} else {
			msg("No products found", 'error');
		}

		$this->parseNotificationsExport();
		return true;
	}

	public function parseNotificationsExport()
	{
		$month = date("m");
		$year = date("Y");

		for ($i = 0; $i < 24; $i++) {

			$time = strtotime("$year-$month-01");

			$date = array(
				"from" => date("Y-m-01", $time),
				"to" => date("Y-m-t", $time),
				"label" => date("m/Y", $time),
			);

			$this->tpl->assign("DATE", $date);
			$this->tpl->parse("content.stock_period");

			$month--;
			if ($month == 0) {
				$year--;
				$month = 12;
			}

		}
	}

}		
