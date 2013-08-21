<?php
/** 
 * Copyright (c) 2012-2013 Laposa Ltd (http://laposa.co.uk)
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
	 * commonCSVAction
	 */
	 
	public function commonCSVAction($records, $filename = 'export') {
		
		if (is_array($records)) {
		
			// parse records to CSV format
			$this->parseCSVTemplate($records);
			
			// set HTTP headers for the output
			$this->sendCSVHeaders($filename);
			
		} else {
			
			echo "no records"; exit;
		
		}
		
	}
	
	/**
	 * parseCSVTemplate
	 */
	 
	public function parseCSVTemplate($records) {
		
		if (!is_array($records)) return false;
		
		/**
		 * parse records
		 */
		 
		$header = 0;
		
		foreach ($records as $record) {
			
			/**
			 * Create CSV header
			 */
			if ($header == 0) {
			
				foreach ($record as $key=>$val) {
			
					$column['name'] = $key;
			
					$this->tpl->assign('COLUMN', $column);
					$this->tpl->parse('content.th');
				}
				$header = 1;
			}
        
			foreach ($record as $key=>$val) {
			
				if (!is_numeric($val)) {
			
					$val = addslashes($val);
					$val = '"' . $val . '"';
					$val = preg_replace("/[\n\r]/", '', $val);
				}

				$this->tpl->assign('value', $val);
				$this->tpl->parse('content.item.attribute');
			}
	
			$this->tpl->parse('content.item');
		}
				
	}
	
	/**
	 * sendCSVHeaders (HTTP header)
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
