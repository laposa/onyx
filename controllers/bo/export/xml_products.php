<?php
/** 
 * Products XML export
 *
 * Copyright (c) 2009-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Export_Xml_Products extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        $product_list = $this->getProductList();
        //print_r($product_list); exit;
        
        $this->parseProductListTemplate($product_list);
        
        $this->beforeOutputProductList();

        return true;
    }
    
    /**
     * get order list
     */
     
    function getProductList() {
    
        /**
         * get filter
         */
         
        if ($_POST['product-list-filter']) $filter = $_POST['product-list-filter'];
        else if (is_array($_SESSION['bo']['product-list-filter'])) $filter = $_SESSION['bo']['product-list-filter'];
        else $filter = array();
        
        
        /**
         * Initialize order object
         */
        require_once('models/ecommerce/ecommerce_product.php');
        
        $Product = new ecommerce_product();

        
        /**
         * Get order list
         */
        $product_list = $Product->getFilteredProductList($filter);
        $product_list = array_reverse($product_list);
        
        return $product_list;

    }
    
    
    /**
     * parse template
     */

    function parseProductListTemplate($list) {
        if (is_array($list)) {
            foreach ($list as $item) {
                //print_r($order_item);
                $this->tpl->assign("ITEM", $item);
                $this->tpl->parse("content.item");
            }
        }
    }
    
    /**
     * output
     */
     
    function beforeOutputProductList() {
        header('Content-Type: text/xml; charset=UTF-8');
    }
}
