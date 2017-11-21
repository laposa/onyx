<?php
/** 
 * Copyright (c) 2008-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/export/csv.php');

class Onxshop_Controller_Bo_Export_CSV_Customers extends Onxshop_Controller_Bo_Export_CSV {

    /**
     * main action
     */
     
    public function mainAction() {
        
        set_time_limit(0);
        
        require_once('models/client/client_customer.php');
        
        $this->Customer = new client_customer();
        
        /**
         * Get customer list filter
         */
        
        $customer_filter = $_SESSION['bo']['customer-filter'];
        
        /**
         * Get customer list
         */
        
        $records = $this->Customer->getClientList($customer_filter);
        if (ONXSHOP_ECOMMERCE) $stores = $this->getStores();
        $categories = $this->getCategories();

        if (is_array($records)) {
        
                /**
                 * preprocess
                 */
                foreach ($records as $i => $record) {

                    $records[$i]['status'] = $this->getStatusName($record['status']);
                    $records[$i]['newsletter'] = $record['newsletter'] == 1 ? 'yes' : 'no';
                    if (ONXSHOP_ECOMMERCE) $records[$i]['store_title'] = $stores[$record['store_id']]['title'];
                    if (ONXSHOP_ECOMMERCE) $records[$i]['store_code'] = $stores[$record['store_id']]['code'];
                    if (is_array($categories[$record['customer_id']]))
                        $records[$i]['categories'] = implode(", ", $categories[$record['customer_id']]);
                    else 
                        $records[$i]['categories'] = "";

                }

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
            $this->sendCSVHeaders('customers');
        
        } else {
        
            echo "no records"; exit;
        
        }

        return true;
    }

    protected function getStatusName($status_id) {

        switch ($status_id) {
            case 0: return 'disabled';
            case 1: return 'registered';
            case 2: return 'reserved';
            case 3: return 'preserved';
            case 4: return 'deleted';
        }

        return 'unknown';
    }

    protected function getStores() {

        $sql = "SELECT id, title, code FROM ecommerce_store";
        $records = $this->Customer->executeSql($sql);
        $result = array();
        foreach ($records as $item) $result[$item['id']] = array('title'=>$item['title'], 'code'=>$item['code']);
        return $result;
    }   

    protected function getCategories() {

        $sql = "SELECT t.node_id, l.title 
            FROM client_customer_taxonomy AS t
            LEFT JOIN common_taxonomy_tree AS r ON r.id = t.taxonomy_tree_id
            LEFT JOIN common_taxonomy_label AS l ON l.id = r.label_id
            ";
        $records = $this->Customer->executeSql($sql);
        $result = array();
        foreach ($records as $item) $result[$item['node_id']][] = $item['title'];

        return $result;

    }

}
