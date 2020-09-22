<?php
/**
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Component_Ecommerce_Product_List_Sorting extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * Ordering
         */
        
        /**
        * &#9650; up
        * &#9654; right 
        * &#9660; down
        * &#9664; left
        &uarr;
        &darr;
        */
        
        /**
         * read input variables
         */
         
        $sort_by = $this->GET['sort']['by'];
        $sort_direction = $this->GET['sort']['direction'];
        
        /**
         * set basic variables
         */
         
        $popularity = array();
        $price = array();
        $name = array();
        
        //default values
        $popularity['direction'] = "DESC";
        $price['direction'] = "ASC";
        $name['direction'] = "ASC";
        $created['direction'] = "DESC";
        
        /**
         * process reorder
         */
         
        switch ($sort_by) {
        
            case 'popularity':
                if ($sort_direction == 'ASC') {
                    $popularity['arrow'] = "&#9650;";
                    $popularity['direction'] = "DESC";
                    $popularity['title'] = I18N_PRODUCT_LIST_SORT_POPULARITY_CLICK_POPULARITY_HIGH_LOW;
                    
                    $active['title'] = I18N_PRODUCT_LIST_SORT_POPULARITY_SORTED_POPULARITY_LOW_HIGH;
                } else {
                    $popularity['arrow'] = "&#9660;";
                    $popularity['direction'] = "ASC";
                    $popularity['title'] = I18N_PRODUCT_LIST_SORT_POPULARITY_CLICK_POPULARITY_LOW_HIGH;
                    
                    $active['title'] = I18N_PRODUCT_LIST_SORT_POPULARITY_SORTED_POPULARITY_HIGH_LOW;
                }
                
                $popularity['class'] = 'active';
                
                $price['arrow'] = "";
                $price['title'] = I18N_PRODUCT_LIST_SORT_POPULARITY_CLICK_PRICE_LOW_HIGH;
                $name['arrow'] = "";
                $name['title'] = I18N_PRODUCT_LIST_SORT_POPULARITY_CLICK_NAME_A_Z;
                $created['arrow'] = "";
                $created['title'] = I18N_PRODUCT_LIST_SORT_POPULARITY_CLICK_CREATED_Z_A;
                break;
                
            case 'price':
                if ($sort_direction == 'ASC') {
                    $price['arrow'] = "&#9650;";
                    $price['direction'] = "DESC";
                    $price['title'] = I18N_PRODUCT_LIST_SORT_PRICE_CLICK_PRICE_HIGH_LOW;
                    
                    $active['title'] = I18N_PRODUCT_LIST_SORT_PRICE_SORTED_PRICE_LOW_HIGH;
                } else {
                    $price['arrow'] = "&#9660;";
                    $price['direction'] = "ASC";
                    $price['title'] = I18N_PRODUCT_LIST_SORT_PRICE_CLICK_SORT_PRICE_LOW_HIGH;
                    
                    $active['title'] = I18N_PRODUCT_LIST_SORT_PRICE_SORTED_PRICE_HIGH_LOW;
                }
                
                $price['class'] = 'active';
                
                $popularity['arrow'] = "";
                $popularity['title'] = I18N_PRODUCT_LIST_SORT_PRICE_CLICK_POPULARITY_MOST_LEAST;
                $name['arrow'] = "";
                $name['title'] = I18N_PRODUCT_LIST_SORT_PRICE_CLICK_NAME_A_Z;
                $created['arrow'] = "";
                $created['title'] = I18N_PRODUCT_LIST_SORT_PRICE_CLICK_CREATED_Z_A;
                break;
                
            case 'name':
                if ($sort_direction == 'ASC') {
                    $name['arrow'] = "&#9650;";
                    $name['direction'] = "DESC";
                    $name['title'] = I18N_PRODUCT_LIST_SORT_NAME_CLICK_NAME_Z_A;
                    
                    $active['title'] = I18N_PRODUCT_LIST_SORT_NAME_SORTED_NAME_A_Z;
                } else {
                    $name['arrow'] = "&#9660;";
                    $name['direction'] = "ASC";
                    $name['title'] = I18N_PRODUCT_LIST_SORT_NAME_CLICK_NAME_A_Z;
                    
                    $active['title'] = I18N_PRODUCT_LIST_SORT_NAME_SORTED_NAME_Z_A;
                }
                
                $name['class'] = 'active';
                
                $popularity['arrow'] = "";
                $popularity['title'] = I18N_PRODUCT_LIST_SORT_NAME_CLICK_POPULARITY_MOST_LEAST;
                $price['arrow'] = "";
                $price['title'] = I18N_PRODUCT_LIST_SORT_NAME_CLICK_PRICE_LOW_HIGH;
                $created['arrow'] = "";
                $created['title'] = I18N_PRODUCT_LIST_SORT_NAME_CLICK_CREATED_Z_A;
                break;
            
            case 'created':
            default:
                if ($sort_direction == 'ASC') {
                    $created['arrow'] = "&#9650;";
                    $created['direction'] = "DESC";
                    $created['title'] = I18N_PRODUCT_LIST_SORT_CREATED_CLICK_CREATED_Z_A;
                    
                    $active['title'] = I18N_PRODUCT_LIST_SORT_CREATED_SORTED_CREATED_A_Z;
                } else {
                    $created['arrow'] = "&#9660;";
                    $created['direction'] = "ASC";
                    $created['title'] = I18N_PRODUCT_LIST_SORT_CREATED_CLICK_CREATED_A_Z;
                    
                    $active['title'] = I18N_PRODUCT_LIST_SORT_CREATED_SORTED_CREATED_Z_A;
                }
                
                $created['class'] = 'active';
                
                $popularity['arrow'] = "";
                $popularity['title'] = I18N_PRODUCT_LIST_SORT_CREATED_CLICK_POPULARITY_MOST_LEAST;
                $price['arrow'] = "";
                $price['title'] = I18N_PRODUCT_LIST_SORT_CREATED_CLICK_PRICE_LOW_HIGH;
                $name['arrow'] = "";
                $name['title'] = I18N_PRODUCT_LIST_SORT_CREATED_CLICK_NAME_A_Z;
                break;
                
        }
        
        $this->tpl->assign("ACTIVE", $active);
        
        $this->tpl->assign("POPULARITY", $popularity);
        $this->tpl->assign("PRICE", $price);
        $this->tpl->assign("NAME", $name);
        $this->tpl->assign("CREATED", $created);

        return true;
    }
}
