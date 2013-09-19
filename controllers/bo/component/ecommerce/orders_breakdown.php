<?php
/**
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Orders_Breakdown extends Onxshop_Controller {

	protected $home_country_name;
	protected $home_country_id;
	protected $eu_members;
	protected $prouduct_types;

	/**
	 * main action
	 */
	 
	public function mainAction()
	{

		$this->initModels();

		// init variables
		$this->home_country_id = $this->getHomeCountryId();
		$this->home_country_name = $this->getHomeCountryName($this->home_country_id);
		$this->eu_members = $this->getEuMembers();
		$this->prouduct_types = $this->getProductTypes();

		// load raw data
		$range = $this->getDateRange();
		$sales = $this->Order->getSales($range['from'], $range['to']);

		if ($sales['all']) {

			// process data
			$invoices = $this->groupByInvoice($sales['all']);
			$breakdown = $this->processInvoices($invoices);

			// prase template
			$this->parseResults($breakdown, $sales['transactions_total'], $sales['invoices_total']);

		} else {

			msg("Sorry, no data available for selected period.");

		}

		return true;

	}

	/**
	 * initialize model instanced in the class scope
	 */
	protected function initModels()
	{
		require_once('models/ecommerce/ecommerce_order.php');
		require_once('models/ecommerce/ecommerce_product_type.php');
		require_once('models/international/international_country.php');
		$this->Order = new ecommerce_order();
		$this->ProductType = new ecommerce_product_type();
		$this->Country = new international_country();
	}	

	/**
	 * parse template
	 */
	protected function parseResults(&$breakdown, $transactions_total, $invoices_total)
	{
		// parse goods
		foreach ($breakdown['goods'] as $product_type_id => $type) {

			$this->tpl->assign("TYPE_NAME", $this->prouduct_types[$product_type_id]['name']);

			foreach ($type as $delivery_name => $vat_group) {

				foreach ($vat_group as $vat_rate => $row) {

					$row['delivery'] = $delivery_name;
					$row['vat_rate'] = $vat_rate;
					$row['gross_post_discount'] = $row['gross_pre_discount'] + $row['discount'];
					$row['net_post_discount'] = $row['gross_post_discount'] / ((100 + $vat_rate) / 100);
					$row['vat'] = $row['gross_post_discount'] - $row['net_post_discount'];

					$totals['gross_pre_discount'] += $row['gross_pre_discount'];
					$totals['discount'] += $row['discount'];
					$totals['gross_post_discount'] += $row['gross_post_discount'];
					$totals['net_post_discount'] += $row['net_post_discount'];
					$totals['vat'] += $row['vat'];

					$this->tpl->assign("ROW", $row);
					$this->tpl->parse("content.result.row");

				}	
			}

		}

		// parse delivery
		foreach ($breakdown['delivery'] as $delivery_name => $vat_group) {

			$this->tpl->assign("TYPE_NAME", 'Delivery');

			foreach ($vat_group as $vat_rate => $row) {

				$row['delivery'] = $delivery_name;
				$row['vat_rate'] = $vat_rate;
				$row['gross_pre_discount'] = $row['net_post_discount'] + $row['vat'];
				$row['discount'] = 0;
				$row['gross_post_discount'] = $row['gross_pre_discount'];

				$totals['gross_pre_discount'] += $row['gross_pre_discount'];
				$totals['discount'] += $row['discount'];
				$totals['gross_post_discount'] += $row['gross_post_discount'];
				$totals['net_post_discount'] += $row['net_post_discount'];
				$totals['vat'] += $row['vat'];

				$this->tpl->assign("ROW", $row);
				$this->tpl->parse("content.result.row");
	
			}

		}

		$this->tpl->assign("TOTALS", $totals);
		$this->tpl->assign("TRANSACTIONS_TOTAL", $transactions_total);
		$this->tpl->assign("INVOICES_TOTAL", $invoices_total);
		$this->tpl->parse('content.result.totals');

		$this->tpl->parse('content.result');
	}

	/**
	 * process invoices
	 */
	protected function processInvoices(&$invoices)
	{
		$breakdown = array();

		foreach ($invoices as $invoice) {

			$invoice_gross = 0;
			$delivery = $invoice['delivery'];

			// interate through invoice items - first round
			foreach ($invoice['items'] as $item) {

				$type = $item['product_type_id'];
				if ($delivery == 'World' && $item['goods_vat_sr'] == 0) $vat = 0;
				else $vat = $this->prouduct_types[$type]['vat'];

				$item_gross = $item['quantity'] * $item['price'] * ((100 + $vat) / 100);
				if ($item_gross > 0) {
					$breakdown['goods'][$type][$delivery][$vat]['gross_pre_discount'] += $item_gross;
					$invoice_gross += $item_gross;
				}

			}

			// if needed interate through invoice items second time to apply discount evenly
			if ($invoice['items'][0]['discount'] > 0) {

				foreach ($invoice['items'] as $item) {

					$type = $item['product_type_id'];
					if ($delivery == 'World' && $item['goods_vat_sr'] == 0) $vat = 0;
					else $vat = $this->prouduct_types[$type]['vat'];

					$item_gross = $item['quantity'] * $item['price'] * ((100 + $vat) / 100);
					$goods_discount = min($invoice_gross, $item['discount']);
					$discount = $goods_discount * ($item_gross / $invoice_gross);
					if ($item_gross > 0) {
						$breakdown['goods'][$type][$delivery][$vat]['discount'] -= $discount;
					}

				}
			}

			// process delivery
			if ($invoice['items'][0]['delivery_net'] > 0) {
				$vat = round($invoice['items'][0]['delivery_vat'] / $invoice['items'][0]['delivery_net'] * 100);
				$breakdown['delivery'][$delivery][$vat]['net_post_discount'] += $invoice['items'][0]['delivery_net'];
				$breakdown['delivery'][$delivery][$vat]['vat'] += $invoice['items'][0]['delivery_vat'];

			}
		}

		return $breakdown;
	}

	/**
	 * prepare EU member list
	 */
	protected function getEuMembers()
	{
		$records = $this->Country->listing("eu_status = TRUE");
		$result = array();
		foreach ($records as $item) $result[$item['id']] = $item['id'];
		return $result;
	}

	/**
	 * Get home country id
	 */
	protected function getHomeCountryId()
	{
		$conf = international_country::initConfiguration();
		return $conf['default_id'];
	}

	/**
	 * Get home country name
	 */
	protected function getHomeCountryName($home_country_id)
	{
		$record = $this->Country->listing("id = {$home_country_id}");
		return $record[0]['iso_code2'] == 'GB' ? 'UK' : $record[0]['iso_code2'];
	}

	/**
	 * Prepare prouduct types as associative array
	 */
	protected function getProductTypes()
	{
		$records = $this->ProductType->listing('publish = 1', 'id ASC');

		$types = array();
		foreach ($records as $item) $types[$item['id']] = $item;

		return $types;
	}

	/**
	 * prepare date range
	 */
	protected function getDateRange()
	{
		if (is_array($this->GET['reports-filter'])) {
			
			$range['from'] = $this->GET['reports-filter']['from'];
			$range['to'] = $this->GET['reports-filter']['to'];
			
		} else if (is_array($_SESSION['reports-filter'])) {
			$range['from'] = $_SESSION['reports-filter']['from'];
			$range['to'] = $_SESSION['reports-filter']['to'];
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
			$range['from'] = "$year_previous_month-$previous_month-01";
			$range['to'] = "$this_year-$this_month-01";
		}

		return $range;
	}


	/**
	 * convert given one dimensional product sales list to two dimensional 
	 * array by invoice, use invoice_id as key
	 */
	protected function groupByInvoice(&$sales)
	{
		$invoices = array();

		foreach ($sales as $item) {

			$i = $item['invoice_id'];

			$invoices[$i]['items'][] = $item;

			if (!isset($invoices[$i]['delivery'])) {

				if ($item['delivery_country_id'] == $this->home_country_id) {
					$invoices[$i]['delivery'] = $this->home_country_name;
				} else if ($this->eu_members[$item['delivery_country_id']] == $item['delivery_country_id']) {
					$invoices[$i]['delivery'] = 'EU';
				} else {
					$invoices[$i]['delivery'] = 'World';
				}

			}
		}

		return $invoices;
	}

}		
