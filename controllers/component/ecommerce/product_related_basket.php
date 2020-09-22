<?php
/** 
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * TODO: remove duplicates and don't displays items which are already in basket
 */

class Onyx_Controller_Component_Ecommerce_Product_Related_Basket extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * create objects
         */
         
        require_once('models/ecommerce/ecommerce_basket.php');
        require_once('models/ecommerce/ecommerce_product_to_product.php');
        $Basket = new ecommerce_basket();
        $PtP = new ecommerce_product_to_product();
        
        /**
         * Set variables
         */
        
        switch ($this->GET['type']) {
            case 'static':
                $type = 'static';
                $this->tpl->assign("TITLE", I18N_RELATED_PRODUCTS_STATIC);
            break;
            case 'dynamic':
            default:
                $type = 'dynamic';
                $this->tpl->assign("TITLE", I18N_RELATED_PRODUCTS_DYNAMIC);
            break;
        }
        
        $related = array();
        
        //limit for each product - how many related products to show to each product in basket
        if (is_numeric($this->GET['limit_each'])) $limit_each = $this->GET['limit_each'];
        else $limit_each = 2;
        
        /**
         * get product list
         */

        $basket_content_ids = $Basket->getContentItemsProductIdList($_SESSION['basket']['id']);
        
        /**
         * Get list
         */
        
        if (is_array($basket_content_ids)) {
        
            foreach ($basket_content_ids as $id) {
        
                $related_to_one = $PtP->getRelatedProduct($id, $limit_each, $type);
                
                if (is_array($related_to_one)) {
                    //make sure we don't add duplicates
                    foreach ($related_to_one as $item_one) {
                        if (!is_array($related['product_list'])) {
                            $related[] = $item_one;
                        } else {
                            $exists = 0;
                            foreach ($related['product_list'] as $item) {
                                if ($item['id'] == $item_one['id']) $exists = 1;
                            }
                            if ($exists == 0) $related[] = $item_one;
                        }
                    }
                }
            }
        }
        
        /**
         * Pass product_id_list to product_list controller
         */
        
        if (is_array($related) && count($related) > 0) {
            
            /**
             * prepare HTTP query for product_list component
             */
            
            $related_list['product_id_list'] = $related;
            $query = http_build_query($related_list, '', ':');
            
            /**
             * detect controller for product list
             */
    
            switch ($this->GET['template']) {
                case 'scroll':
                    $controller = 'product_list_scroll';
                    break;
                case '3col':
                    $controller = 'product_list_3columns';
                    break;
                case '2col':
                    $controller = 'product_list_2columns';
                    break;
                case '1col':
                default:
                    $controller = 'product_list';
                    break;
            }
            
            /**
             * call controller
             */
             
            $_Onyx_Request = new Onyx_Request("component/ecommerce/$controller~{$query}:image_width={$this->GET['image_width']}~");
            $this->tpl->assign('ITEMS', $_Onyx_Request->getContent());
            $this->tpl->parse('content.product_related');
        }

        return true;
    }
}
