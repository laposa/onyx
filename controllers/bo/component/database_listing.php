<?php
/** 
 * Copyright (c) 2008-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Database_Listing extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * getting detail of model
		 */
		 
		if ($this->GET['model'] != '') {
		
			$model_file = $this->GET['model'];
			$dir = explode("/", $model_file);
		
			$path = "models/$model_file";
			//$real_path = realpath($path);
		
			if (!is_dir(ONXSHOP_DIR . $path) && (is_file(ONXSHOP_DIR . "$path.php") || is_file(ONXSHOP_PROJECT_DIR . "$path.php"))) {
		
				require_once("$path.php");
			
		
				//$model = preg_replace('/\.php/', '', $dir[1]);
				$model = $dir[1];
				$Obj = new $model;
			
				/**
				 * get table size
				 */
				 
				$table_size = $Obj->getTableSize();
				$this->tpl->assign('TABLE_SIZE', $table_size);
				
				$this->tpl->assign('MODEL', $model);
		
				/**
				 * creating head
				 */
				 
				$columns = $Obj->getTableInformation($model);
				//print_r($columns );
				foreach ($columns as $col) {
					$column['name'] = $col['COLUMN_NAME'];
					$column['type'] = $col['DATA_TYPE'];
				
					$this->tpl->assign('COLUMN', $column);
					$this->tpl->parse('content.listing.th');
				}
		
		
				/**
				 * display records
				 */
				 
				//pagination
				if (is_numeric($this->GET['limit_from']) && is_numeric($this->GET['limit_per_page'])) {
				    $from = $this->GET['limit_from'];
				    $per_page = $this->GET['limit_per_page'];
				} else {
				    $from = 0;
				    $per_page = 10;
				}
		
				$limit = "$from,$per_page";
		
				$records = $Obj->listing('', 'id ASC', $limit);
		
				foreach ($records as $record) {
					//$this->tpl->assign('record', $record);
					foreach ($record as $key=>$val) {
						//don't display whole record of serialized content
						if ($Obj->_metaData[$key]['validation'] == 'serialized') {
							//A) display only part
							//$val = substr($val, 0, 100) . "...";
							//B) display human redabe
							$val = print_r(unserialize($val), true);
							
						}
						$this->tpl->assign('value', $val);
						$this->tpl->parse('content.listing.item.attribute');
					}
			
					$this->tpl->parse('content.listing.item');
				}
				
		
				//count records
				$count = $Obj->count();
				$this->tpl->assign('COUNT_RECORD', $count);
		
				//pagination
				$_Onxshop_Request = new Onxshop_Request("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count~");
				$this->tpl->assign('PAGINATION', $_Onxshop_Request->getContent());
		
		
				$this->tpl->parse('content.listing');
		
		
			} else {
				$this->tpl->parse('content.hint');
			}
		}

		return true;
	}
}
		
