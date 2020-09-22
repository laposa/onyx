<?php
/** 
 * Copyright (c) 2008-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/export/csv.php');

class Onxshop_Controller_Bo_Export_CSV_Promotion extends Onxshop_Controller_Bo_Export_CSV {

    /**
     * main action
     */
     
    public function mainAction() {
        
        set_time_limit(0);
        
        require_once('models/ecommerce/ecommerce_promotion.php');
    
        $this->Promotion = new ecommerce_promotion();
        
        /**
         * Get the list
         */
        
        $records = $this->Promotion->getList(0, 100000, $_SESSION['bo']['voucher-filter']);

        if (is_array($records)) {
        
                /**
                 * parse records
                 */
                $header = 0;
                
                foreach ($records as $record) {

                    $record = array_merge($record, $record['usage']);
                    $record['generated_by_customer'] = $record['customer_title_before'] .
                        $record['customer_first_name'] .
                        $record['customer_last_name'];
                    $record['invites_sent'] = $record['customer_invite_count'];
                    $record['net_sale_before_deducted_discount'] = $record['sum_goods_net'];
                    $record['discount'] = $record['sum_discount_net'];

                    unset($record['usage']);
                    unset($record['other_data']);
                    unset($record['description']);
                    unset($record['publish']);
                    unset($record['customer_account_type']);
                    unset($record['discount_fixed_value']);
                    unset($record['discount_percentage_value']);
                    unset($record['discount_free_delivery']);
                    unset($record['uses_per_coupon']);
                    unset($record['uses_per_customer']);
                    unset($record['limit_list_products']);
                    unset($record['limit_delivery_country_id']);
                    unset($record['limit_delivery_carrier_id']);
                    unset($record['generated_by_order_id']);
                    unset($record['generated_by_customer_id']);
                    unset($record['limit_by_customer_id']);
                    unset($record['limit_to_first_order']);
                    unset($record['limit_to_order_amount']);
                    unset($record['customer_title_before']);
                    unset($record['customer_first_name']);
                    unset($record['customer_last_name']);
                    unset($record['customer_invite_count']);
                    unset($record['count']);
                    unset($record['sum_goods_net']);
                    unset($record['sum_discount_net']);

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
}
