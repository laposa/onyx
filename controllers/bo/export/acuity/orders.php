<?php
/** 
 * Orders export for Acuity Solutions Limited
 
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/export/xml_orders.php');

class Onxshop_Controller_Bo_Export_Acuity_Orders extends Onxshop_Controller_Bo_Export_Xml_Orders {
//TODO: remove tabs before parsing

	/**
	 * parse item
	 */

	function parseOrderListItem($item) {
	
		//print_R($item);
		
		if (is_array($item)) {
		
			$this->tpl->assign("ORDER_ITEM", $item);
			
			//LineType field which indicates whether the line is a stock item (0), text/service with price (1), additional charge (2) – delivery, packaging etc... or a text comment (3).
			
			/**
			 * basket items (LineType = 0)
			 */
		
			foreach ($item['basket']['items'] as $basket_item) {
				
				$acuity_item = $this->mapBasketLineToAcuityCSV($basket_item);
				$this->tpl->assign("ACUITY_ITEM", $acuity_item);
				$this->tpl->parse("content.order_item");
			}
			
			

			/**
			 * delivery charge (LineType = 2)
			 */
			 
			$acuity_item = $this->mapDeliveryLineToAcuityCSV($item);
			$this->tpl->assign('ACUITY_ITEM', $acuity_item);
			$this->tpl->parse("content.order_item");
		}
	}
	
	/**
	 * output
	 */
	 
	function beforeOutputOrderList() {
		//header('Content-Type: text/xml; charset=UTF-8');
	}
	
	
	/**
	 * map basket line to Acuity CSV
	 */
	 
	public function mapBasketLineToAcuityCSV($item) {
	
			//UniqueOrderRef
			//OrderType
			//CustomerCode
			//AccountName
			//CountryCode
			//ContactName
			//TelephoneNumber
			//AddressLine1
			//AddressLine2
			//AddressLine3
			//AddressLine4
			//PostCode
			//Email
			//DocDate
			//RequestedDate
			//PromisedDate
			//UseInvoiceAddress
			//DeliveryName
			//DeliveryAdd1
			//DeliveryAdd2
			//DeliveryAdd3
			//DeliveryAdd4
			//Analysis1
			//Analysis2
			//Analysis3
			//Analysis4
			//Analysis5
			//Analysis6
			//DeliveryAddPostCode
			//DocNo
			//SecRef
			//PaymentMethod
			//SpareText1
			//SpareText2
			//SpareText3
			//SpareNumber1
			//SpareNumber2
			//SpareNumber3
			//SpareBit1
			//SpareBit2
			//SpareBit3
			//SpareDate1
			//SpareDate2
			//SpareDate3
			//isReadyToImport
			$acuity_line['UniqueLineID'] = $item['id'];
			//UniqueOrderRef
			$acuity_line['LineType'] = 0;
			$acuity_line['ItemCode'] = $item['product']['variety']['sku'];
			$acuity_line['ItemDescription'] = "{$item['product']['name']} / {$item['product']['variety']['name']}";
			$acuity_line['LineQTY'] = $item['quantity'];
			$acuity_line['TaxCode'] = $item['product']['vat'];
			//NLCode
			//NLCostCentre
			//NLDepartment
			$acuity_line['UnitSellingPrice'] = $item['product']['variety']['price']['value_net'];
			//SpareNumber1
			//SpareText1
			//SpareText2
			//SpareText3
			//SpareNumber2
			//SpareNumber3
			//SpareBit1
			//SpareBit2
			//SpareBit3
			//SpareDate1
			//SpareDate2
			//SpareDate3
			//AnalysisCode1
			//AnalysisCode2
			//AnalysisCode3
			//AnalysisCode4
			//AnalysisCode5
			//AnalysisCode6
			//UnitDiscountPercent
			
			return $acuity_line;
	}
	
	/**
	 * map delivery line to Acuity CSV
	 */
	 
	public function mapDeliveryLineToAcuityCSV($item) {
			
			$acuity_line['UniqueLineID'] = $item['basket']['delivery']['id'];//FIX
			//UniqueOrderRef
			$acuity_line['LineType'] = 2;
			$acuity_line['ItemCode'] = "DELIVERY_{$item['basket']['delivery']['carrier_id']}";
			$acuity_line['ItemDescription'] = "{$item['basket']['delivery']['carrier_detail']['title']}";
			$acuity_line['LineQTY'] = 1;
			$acuity_line['TaxCode'] = $item['basket']['delivery']['vat_rate'];
			//NLCode
			//NLCostCentre
			//NLDepartment
			$acuity_line['UnitSellingPrice'] = $item['basket']['delivery']['value_net'];
			
			return $acuity_line;
	}
	
}