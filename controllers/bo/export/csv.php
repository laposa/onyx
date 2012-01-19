<?php
/** 
 * Copyright (c) 2012 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Export_CSV extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		//set the headers for the output
		$this->sendCSVHeaders('filename');

		return true;
		
	}
	
	/**
	 * sendCSVHeaders
	 */
	 
	public function sendCSVHeaders($filename = 'unknown') {
		
		//set the headers for the output
	    /*
	    UTF16 for excel
	    header( "Content-type: application/vnd.ms-excel; charset=UTF-16LE" );
	    header('Content-Disposition: attachment; filename="export.csv"');
	    echo chr(255).chr(254).mb_convert_encoding( $vypis_csv, 'UTF-16LE', 'UTF-8′);*/
		header('Content-type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="'.$filename.'-'.date('Y\-m\-d\_Hi').'.csv"');
		header("Cache-Control: cache, must-revalidate");
		header("Pragma: public");
		
	}
}
