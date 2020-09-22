<?php
/** 
 * Orders export
 *
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

set_time_limit(0);

class Onxshop_Controller_Bo_Export_Xml_Orders extends Onxshop_Controller {

    /**
     * main action
     */
        
    public function mainAction() {
        
        $order_list = $this->getOrderList();
        //print_r($order_list); exit;
        
        $this->parseOrderListTemplate($order_list);
        
        $this->beforeOutputOrderList();

        return true;
    }
    
    /**
     * get order list
     */

    function getOrderList() {

        require_once('models/ecommerce/ecommerce_order.php');
        
        $Order = new ecommerce_order();
        
        $filter = array();
        if (is_numeric($this->GET['status'])) $filter['status'] = $this->GET['status'];
        else $filter['status'] = 1;
        
        $customer_id = NULL;
        
        $order_list = $Order->getFullDetailList($customer_id, $filter);
        
        return $order_list;
        

    }
    
    
    /**
     * parse template
     */

    function parseOrderListTemplate($list) {
    
        if (is_array($list)) {
    
            foreach ($list as $order_item) {
            
                //print_r($order_item);
                $this->parseOrderListItem($order_item);
                
            }
        }
    }
    
    /**
     * parse item
     */

    function parseOrderListItem($item) {
    
        if (is_array($item)) {
            
            foreach ($item['basket']['items'] as $basket_item) {
                $this->tpl->assign("BASKET_ITEM", $basket_item);
                $this->tpl->parse("content.order_item.basket_item");
            }
            
            $this->tpl->assign("ORDER_ITEM", $item);
            $this->tpl->parse("content.order_item");

        }
    }
    
    /**
     * output
     */
     
    function beforeOutputOrderList() {
    
        header('Content-Type: text/xml; charset=UTF-8');
    }
}
