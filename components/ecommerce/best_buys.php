<?php
/** 
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details. 
 * 
 */

class Onyx_Controller_Component_Ecommerce_Best_Buys extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * create object
         */
         
        require_once('models/ecommerce/ecommerce_product.php');
        $Product = new ecommerce_product();
        
        /**
         * Set Variables
         */
         
        if ($this->GET['type'] == 'worst') $order = 'ASC';
        else $order = 'DESC';
        
        /**
         * number of items limit
         */
        
        if (is_numeric($this->GET['limit'])) $limit = $this->GET['limit'];
        else $limit = 5;
        
        /**
         * period limit in days
         */
         
        if (is_numeric($this->GET['period_limit'])) $period_limit = $this->GET['period_limit'];
        else $period_limit = 7;
        
        /**
         * customer limit
         */
         
        if (is_numeric($this->GET['customer_id'])) $customer_id = $this->GET['customer_id'];
        else if ($this->GET['customer_id'] == 'session') {
            if (!empty($_SESSION['client']['customer']['id'])) {
                $customer_id = $_SESSION['client']['customer']['id'];
            } else {
                //msg("You are not logged in as a customer, displaying normal best buys");
                $customer_id = false;
            }
        } else {
            $customer_id = false;
        }
        
        



         
        /**
         * Get product_list
         */
         
        $product_list = $Product->getMostPopularProducts($order, $limit, $customer_id, $period_limit);
        
        /**
         * if requested product sales result is empty, recalculate with 31 days period limit
         */
          
        if (count($product_list) == 0) {
            $period_limit = 31;
            $product_list = $Product->getMostPopularProducts($order, $limit, $customer_id, $period_limit);
        }
        
        /**
         * if product sales in last 31 is empty, recalculate with 356 days period limit
         */
          
        if (count($product_list) == 0) {
            $period_limit = 356;
            $product_list = $Product->getMostPopularProducts($order, $limit, $customer_id, $period_limit);
        }
        
        /**
         * if product sales in last 356 is empty, recalculate with no period limit
         */
          
        if (count($product_list) == 0) {
            $period_limit = 0;
            $product_list = $Product->getMostPopularProducts($order, $limit, $customer_id, $period_limit);
        }
        
        
        /**
         * detect controller (template) for product list
         */

        switch ($this->GET['template']) {
            case 'scroll':
                $controller = 'product_list_scroll';
                break;
            case '4col':
                $controller = 'product_list_4columns';
                break;
            case '3col':
                $controller = 'product_list_3columns';
                break;
            case '2col':
                $controller = 'product_list_2columns';
                break;
            case '1col':
            default:
                $controller = 'product_list_shelf';
                break;
        }
        
        /**
         * Pass product_id_list to product_list controller
         */
            
        $this->renderList($product_list, $controller);

        return true;
    }
    
    /**
     * render list
     */
     
    public function renderList($product_list, $controller = 'product_list_shelf') {
    
        if (is_array($product_list) && count($product_list) > 0) {
        
            /**
             * prepare HTTP query for product_list component
             */
             
            $bb_list = array();
            foreach ($product_list as $item) {
                $bb_list['product_id_list'][] = $item['product_id'];
            }
            
            //msg(base64_encode(json_encode($bb_list)));
            $query = http_build_query($bb_list, '', ':');
            
            /**
             * call controller
             */
             
            $_Onyx_Request = new Onyx_Request("component/ecommerce/$controller~{$query}~");
            $this->tpl->assign('ITEMS', $_Onyx_Request->getContent());
            $this->tpl->parse('content.best_buys');
        }
    }
}
