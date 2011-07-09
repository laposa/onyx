<?php
/** 
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/ecommerce/sales_report.php');

class Onxshop_Controller_Bo_Export_CSV_Sales extends Onxshop_Controller_Bo_Component_Ecommerce_Sales_Report {

	/**
	 * render list
	 */
	 
	public function renderList($records) {
				
		if (is_array($records)) {
		
				/**
				 * parse records
				 */
				 
				$header = 0;
				
				foreach ($records as $record) {
					/**
					 * Create a header
					 */
					 
					if ($header == 0) {
						foreach ($record as $key=>$val) {
							$column['name'] = $key;
							//$column['type'] = $col->type;
					
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
		
			
			//set the headers for the output
		    /*
		    UTF16 for excel
		    header( "Content-type: application/vnd.ms-excel; charset=UTF-16LE" );
		    header('Content-Disposition: attachment; filename="export.csv"');
		    echo chr(255).chr(254).mb_convert_encoding( $vypis_csv, 'UTF-16LE', 'UTF-8′);*/
			header('Content-type: text/csv; charset=UTF-8');
			header("Content-Disposition: attachment; filename=\"sales-{$_SESSION['reports-filter']['from']}-{$_SESSION['reports-filter']['to']}.csv\"");
			header("Cache-Control: cache, must-revalidate");
			header("Pragma: public");
		} else {
			echo "no records"; exit;
		}

		return true;
	}
}
