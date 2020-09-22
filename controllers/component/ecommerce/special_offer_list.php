<?php
/**
 * Copyright (c) 2013-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/ecommerce/ecommerce_offer.php');

class Onxshop_Controller_Component_Ecommerce_Special_Offer_list extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction()
    {
    
        /**
         * input data
         */
         
        $offer_group_id = (int) $this->GET['offer_group_id'];
        $campaign_category_id = (int) $this->GET['campaign_category_id'];
        $roundel_category_id = (int) $this->GET['roundel_category_id'];
        $template = (string) $this->GET['template'];
        $includeForthcoming = $_SESSION['fe_edit_mode'] == 'edit';

        if ($this->GET['taxonomy_tree_id']) {
            $taxonomy_tree_ids = explode(",", $this->GET['taxonomy_tree_id']);
            if (count($taxonomy_tree_ids) == 0) $taxonomy_tree_ids = array();
        }

        /**
         * initialise and get product list
         */
         
        $Offer = new ecommerce_offer();
        $product_ids = $Offer->getProductIdsForOfferGroup($offer_group_id, $campaign_category_id, 
            $roundel_category_id, $taxonomy_tree_ids, $includeForthcoming);

        if (count($product_ids) > 0) {

            /**
             * build HTTP query
             */
             
            $list = array();
            foreach ($product_ids as $id) $list['product_id_list'][] = $id;
            
            if (isset($this->GET['limit_from'])) $list['limit_from'] = $this->GET['limit_from'];            
            if (isset($this->GET['limit_per_page'])) $list['limit_per_page'] = $this->GET['limit_per_page'];            
            if (isset($this->GET['display_pagination'])) $list['display_pagination'] = $this->GET['display_pagination'];
            if (!isset($this->GET['sort']['by'])) {
                $list['product_id_list_force_sorting_as_listed'] = 1;   
            } else {
                if (isset($this->GET['sort']['by'])) $list['sort']['by'] = $this->GET['sort']['by'];
                if (isset($this->GET['sort']['direction'])) $list['sort']['direction'] = $this->GET['sort']['direction'];           
            }
            
            $query = http_build_query($list, '', ':');

            /**
             * call product_list component
             */
             
            $_Onxshop_Request = new Onxshop_Request("component/ecommerce/product_list_$template~$query~");
            $this->tpl->assign('PRODUCT_LIST', $_Onxshop_Request->getContent());
        }

        return true;
    }

}