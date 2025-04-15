<?php
/**
 * Backoffice product list controller
 *
 * Copyright (c) 2005-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Ecommerce_Product_List extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * Get input variables
         */
         
        if ($_POST['product-list-filter'] ?? false) $filter = $_POST['product-list-filter'];
        else $filter = $_SESSION['bo']['product-list-filter'] ?? [];

        if (is_numeric($this->GET['taxonomy_tree_id'] ?? null)) $filter['taxonomy_json'] = json_encode(array($this->GET['taxonomy_tree_id']));
        else $filter['taxonomy_json'] = false;
        
        /**
         * Get the list
         */
         
        require_once('models/ecommerce/ecommerce_product.php');
        
        $Product = new ecommerce_product(); 
        
        $product_list = $Product->getFilteredProductList($filter);
        
        if (!is_array($product_list)) return false;
        if (count($product_list) == 0) {
            $this->tpl->parse('content.empty_list');
            return true;
        }
        
        /**
         * Sorting
         */
        
        //$_Onyx_Request = new Onyx_Request("component/ecommerce/product_list_sorting");
        //$this->tpl->assign('SORTING', $_Onyx_Request->getContent());
        
        if ($this->GET['product-list-sort-by'] ?? false) {
            $_SESSION['bo']['product-list-sort-by'] = $this->GET['product-list-sort-by'];
        }
        
        if ($this->GET['product-list-sort-direction'] ?? false) {
            $_SESSION['bo']['product-list-sort-direction'] = $this->GET['product-list-sort-direction'];
        }
        
        if ($_SESSION['bo']['product-list-sort-by'] ?? false) {
            $sortby = $_SESSION['bo']['product-list-sort-by'];
        } else {
            $sortby = "modified";
        }
        
        if ($_SESSION['bo']['product-list-sort-direction'] ?? false) {
            $direction = $_SESSION['bo']['product-list-sort-direction'];
        } else {
            $direction = "DESC";
        }
        
        //msg("Sorted by $sortby $direction");
        $product_list_sorted = array();
        
        switch ($sortby) {
        
            default:
            case 'id':
                $product_list = php_multisort($product_list, array(array('key'=>'product_id', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));
                break;
            case 'modified':
                $product_list = php_multisort($product_list, array(array('key'=>'modified', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));
                break;
            case 'product_name':
                $product_list = php_multisort($product_list, array(array('key'=>'product_name', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));   
                break;
            case 'variety_name':
                $product_list = php_multisort($product_list, array(array('key'=>'variety_name', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));   
                break;
            case 'price':
                $product_list = php_multisort($product_list, array(array('key'=>'price', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));  
                break;
            case 'sku':
                $product_list = php_multisort($product_list, array(array('key'=>'sku', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));
                break;
            case 'stock':
                $product_list = php_multisort($product_list, array(array('key'=>'stock', 'sort'=>$direction), array('key'=>'product_id', 'type'=>'numeric')));  
                break;
        }
        
        foreach ($product_list as $item) {
            $product_list_sorted[] = $item;
        }
        
        $product_list = $product_list_sorted;
    
        
        //print_r($product_list);exit;
        
        /**
         * Reformat
         */
         
        $pl = array();
        foreach ($product_list as $item) {
            $pl[$item['product_id']][] = $item;
        }
        $product_list = array();
        foreach ($pl as $item) {
            $product_list[] = $item;
        }
    
        /**
         * Initialize pagination variables
         */
        
        if (is_numeric($this->GET['limit_from'] ?? null)) $from = $this->GET['limit_from'];
        else $from = 0;
        if (is_numeric($this->GET['limit_per_page'] ?? null)) $per_page = $this->GET['limit_per_page'];
        else $per_page = 25;
        
        
        $limit = "$from,$per_page";
        
        
        /**
         * Display pagination
         */
        
        //$link = "/page/" . $_SESSION['active_pages'][0];
        $count = count($product_list);
        
        $_Onyx_Request = new Onyx_Request("component/pagination~link=/request/bo/component/ecommerce/product_list:limit_from=$from:limit_per_page=$per_page:count=$count~");
        $this->tpl->assign('PAGINATION', $_Onyx_Request->getContent());
            
            
        
        /**
         * Parse items
         * Implemented pagination
         */
        
        //print_r($product_list); exit;
         
        $even_odd = '';
        
        foreach ($product_list as $i=>$p_item) {
            
            if ($i >= $from  && $i < ($from + $per_page) ) {
                
                $item = $p_item[0];
                
                $rowspan = count($p_item);
                
                $this->tpl->assign('ROWSPAN', "rowspan='$rowspan'");
                
                $item['disabled'] = ($item['publish']) ? '' : 'disabled';
                        
                $this->tpl->assign('ITEM', $item);
                
                if ($item['image_src']) $this->tpl->parse('content.list.item.imagetitle.image');
                
                $this->tpl->parse('content.list.item.imagetitle');
                
                $even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';
                $this->tpl->assign('CLASS', "class='$even_odd fullproduct'");
                
                foreach ($p_item as $item) {
                    if ($item['variety_publish'] == 0) $item['variety_publish'] = 'disabled';
                    $this->checkNotifications($item);
                    $this->tpl->assign('ITEM', $item);
                    $this->tpl->parse('content.list.item');
                    $this->tpl->assign('CLASS', "class='$even_odd'");
                }
                
            }
        }
        
        $this->tpl->parse('content.list');

        return true;
    }

    /**
     * display confirmation if notifications are about to be sent out
     */
    public function checkNotifications(&$item)
    {
        require_once('models/common/common_watchdog.php');
        $Watchdog = new common_watchdog();

        if ($item['stock'] == 0) {
            $item['num_notifications'] = $Watchdog->checkWatchdog('back_in_stock_customer', $item['variety_id'], 0, 1, true);
        } else {
            $item['num_notifications'] = 0;
        }
    }

}
