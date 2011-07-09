<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Export_CSV_Database extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		set_time_limit(0);
	
		if (file_exists(ONXSHOP_DIR . "models/{$this->GET['model']}.php")) $table = $this->GET['model'];
		else $table = '';
		
		
		//getting detail of model
		if ($table !== '') {
			$model_file = $table;
			$dir = explode("/", $model_file);
		
			$path = "models/$model_file";
			//$real_path = realpath($path);
		
			if (!is_dir(ONXSHOP_DIR . "$path.php")) {
		
				require_once("$path.php");
			
		
				$model = preg_replace('/\.php/', '', $dir[1]);
				$Obj = new $model;
		
				$this->tpl->assign('model', $model);
		
				// creating head
				$columns = $Obj->getTableInformation($model);
				//print_r($columns );
				foreach ($columns as $col) {
					$column['name'] = $col['COLUMN_NAME'];
					$column['type'] = $col['DATA_TYPE'];
				
					$this->tpl->assign('COLUMN', $column);
					$this->tpl->parse('content.th');
				}
		
				// display records
				$records = $Obj->listing();
		
				foreach ($records as $record) {
					//$this->tpl->assign('record', $record);
					foreach ($record as $key=>$val) {
						if ($Obj->_hashMap[$key]['validation'] == 'serialized' || $Obj->_hashMap[$key]['validation'] == 'xhtml') {
							$val = 'N/A';
						}
						//test if it's serialized
						if (is_array(unserialize($val))) $val = preg_replace("/\"/", '', $val);
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
			
			//set the headers for the output
			/*
			UTF16 for excel
			header( "Content-type: application/vnd.ms-excel; charset=UTF-16LE" );
			header('Content-Disposition: attachment; filename="export.csv"');
			echo chr(255).chr(254).mb_convert_encoding( $vypis_csv, 'UTF-16LE', 'UTF-8′);*/
			header('Content-type: text/csv; charset=UTF-8');
			header('Content-Disposition: attachment; filename="'.$model.'-'.date('Y\-m\-d\_Hi').'.csv"');
			header("Cache-Control: cache, must-revalidate");
			header("Pragma: public");
		}

		return true;
	}
}
