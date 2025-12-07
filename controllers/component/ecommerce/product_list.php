<?php
/**
 * Copyright (c) 2010-2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/list.php');

class Onyx_Controller_Component_Ecommerce_Product_List extends Onyx_Controller_List {

    public $Product; 
    
    /**
     * main action (only a router in this case)
     */
     
    public function mainAction() {
    
        // input variables
        $sort_by = null;
        $sort_direction = null; 
        $product_id_list = $this->GET['product_id_list'] ?? null;
        $node_id = $this->GET['node_id'] ?? null;
        $force_sorting_as_listed = isset($this->GET['product_id_list_force_sorting_as_listed']) ? true : false;
        
        if (is_array($this->GET['sort'] ?? null)) {
            $sort_by = $this->GET['sort']['by'];
            $sort_direction = $this->GET['sort']['direction'];
        }

        // initialize product
        require_once('models/ecommerce/ecommerce_product.php');
        $this->Product = new ecommerce_product();
          
        // get product variety list
        if ($node_id) $product_variety_list = $this->getProductVarietyListByNodeId($node_id);
        else if ($product_id_list) $product_variety_list = $this->getProductVarietyListByProductIds($product_id_list, $force_sorting_as_listed);

        $product_list = $this->reformatList($product_variety_list);
        
        // sorting
        if ($sort_by == 'priority') {
            if ($sort_direction == 'ASC') usort($product_list , function ($a, $b) {return $a['node_priority'] <=> $b['node_priority'];});
            else usort($product_list , function ($a, $b) {return $b['node_priority'] <=> $a['node_priority'];});
        }

        // display
        foreach ($product_list as $item) {
            $this->tpl->assign('ITEM', $item);
            if ($item['price_max'] > $item['price_min']) {
                $this->tpl->parse('content.item.price_range');
            } else {
                $this->tpl->parse('content.item.price_single');
            }
            $this->tpl->parse('content.item');
        }

        return true;

    }
    
    /**
     * get product variety list by product ids
     *
     *
     * passed from this controllers:
     * ./component/ecommerce/best_buys.php
     * ./component/ecommerce/recently_viewed_products.php
     * ./component/ecommerce/product_related_basket.php
     * ./component/ecommerce/product_related_to_customer.php
     * ./component/ecommerce/product_related.php
     */
    
    function getProductVarietyListByProductIds($product_id_list, $force_sorting_as_listed) {
        
        // configure filter
        $filter = array();
        $filter['publish'] = 1;
        $filter['product_id_list'] = $product_id_list;
        
        // get list
        $product_variety_list = $this->Product->getFilteredProductList($filter, GLOBAL_LOCALE_CURRENCY);
        
        // modify priority according sort of product_id_list
        if ($force_sorting_as_listed) {
        
            foreach ($product_variety_list as $key=>$item) {
                $product_variety_list[$key]['priority'] = 1000 - array_search($item['product_id'], $product_id_list);
            }
        
        }
        
        // return list
        return $product_variety_list;
    }
    
    /**
     * get product variety list by node_id
     *
     */
    
    function getProductVarietyListByNodeId($node_id) {
        
        if (!is_numeric($node_id)) return false;

        // configure filter
        $filter = array();
        $filter['publish'] = 1;
        $filter['node_id'] = $node_id;

        // get product list
        
        $product_variety_list = $this->Product->getFilteredProductList($filter, GLOBAL_LOCALE_CURRENCY);

        // return list
        return $product_variety_list;
    }

    /**
     * reformat list
     */
    
    public function reformatList($product_variety_list) {

        $product_id_list = [];
        $product_list = [];

        foreach ($product_variety_list AS $item) {
            
            $item['price_max'] = 0; 

            if ($item['node_publish']) {

                if (!in_array($item['product_id'], $product_id_list)) {
                    
                    // new item
                    $product_list[$item['product_id']] = $item;
                    $product_list[$item['product_id']]['price_min'] = $item['price'];
                    $product_list[$item['product_id']]['price_max'] = $item['price'];
                    $product_id_list[] = $item['product_id'];
                    
                } else if ($item['variety_id'] != $product_list[$item['product_id']]['variety_id']) {

                    // existing item, but different variety
                    if ($item['price'] > $product_list[$item['product_id']]['price_max']) {
                        // save max price
                        $product_list[$item['product_id']]['price_max'] = $item['price'];
                    } else if ($item['price'] < $product_list[$item['product_id']]['price_min']) {
                        // save min price
                        $product_list[$item['product_id']]['price_min'] = $item['price'];
                    }
                }
            }
        }

        return $product_list;
    }

    /**
     * findMinMaxPrice
     */

    public function findMinMaxPrice($product_list_item) {

            return $product_list_item;

    }
}
