<?php
/**
 * Products CSV export
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
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
		    /*
		    UTF16 for excel
		    header( "Content-type: application/vnd.ms-excel; charset=UTF-16LE" );
		    header('Content-Disposition: attachment; filename="export.csv"');
		    echo chr(255).chr(254).mb_convert_encoding( $vypis_csv, 'UTF-16LE', 'UTF-8′);*/
			header('Content-type: text/csv; charset=UTF-8');
			header('Content-Disposition: attachment; filename="products-'.date('Y\-m\-d\_Hi').'.csv"');
			header("Cache-Control: cache, must-revalidate");
			header("Pragma: public");
	}
}
