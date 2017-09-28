<?php
/** 
 * Copyright (c) 2006-2012 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/export/csv.php');

class Onxshop_Controller_Bo_Export_CSV_Database extends Onxshop_Controller_Bo_Export_CSV {

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
                        if ($Obj->_metaData[$key]['validation'] == 'serialized' || $Obj->_metaData[$key]['validation'] == 'xhtml') {
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
            $this->sendCSVHeaders($model);
            
        }

        return true;
    }
}
