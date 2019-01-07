<?php
/** 
 * Copyright (c) 2010-2014 Onxshop Ltd (https://onxshop.com)
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
                     * Create header
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
        
            //set the headers for the output
            $filename = "sales-{$_SESSION['bo']['reports-filter']['from']}-{$_SESSION['bo']['reports-filter']['to']}";
            $this->sendCSVHeaders($filename);
            
        } else {
            
            echo "no records"; exit;
        
        }

        return true;
    }
    
    /**
     * sendCSVHeaders
     */
     
    public function sendCSVHeaders($filename = 'unknown') {
        
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
        header("Cache-Control: cache, must-revalidate");
        header("Pragma: public");
        
    }
}
