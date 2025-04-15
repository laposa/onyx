<?php
/**
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/ecommerce/product_list_sorting.php');

class Onyx_Controller_Component_Ecommerce_Product_List_Sorting_Mode extends Onyx_Controller_Component_Ecommerce_Product_List_Sorting {
    
    /**
     * main action
     */
     
    public function mainAction() {
            
        /**
         * read input variables
         */
         
        /**
         * read from session or input
         */
         
        if ($this->GET['product_list_mode']) $product_list_mode = $this->GET['product_list_mode'];
        else if ($_SESSION['product_list_mode']) $product_list_mode = $_SESSION['product_list_mode'];
        else $product_list_mode = 'shelf';
        
        
        /**
         * set basic variables
         */
         
        $shelf = array();
        $grid = array();
        
        
        
        /**
         * process reorder
         */
         
        switch ($product_list_mode) {
        
            case 'grid':
                $grid['class'] = 'active';
            break;
            
            case 'shelf':
            default:    
                $shelf['class'] = 'active';         
            break;
            
        }
        
        $this->tpl->assign("SHELF", $shelf);
        $this->tpl->assign("GRID", $grid);
        

        return true;
    }
}
