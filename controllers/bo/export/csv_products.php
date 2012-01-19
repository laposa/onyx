<?php
/**
 * Products CSV export
 * Copyright (c) 2009-2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/export/xml_products.php');

class Onxshop_Controller_Bo_Export_CSV_Products extends Onxshop_Controller_Bo_Export_Xml_Products {
	
	/**
	 * output
	 */
	 
	function beforeOutputProductList() {
			
			//set the headers for the output
			$this->sendCSVHeaders('products');
			
	}
	
	/**
	 * sendCSVHeaders
	 */
	 
	public function sendCSVHeaders($filename = 'unknown') {
		
		header('Content-type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="'.$filename.'-'.date('Y\-m\-d\_Hi').'.csv"');
		header("Cache-Control: cache, must-revalidate");
		header("Pragma: public");
		
	}
}
