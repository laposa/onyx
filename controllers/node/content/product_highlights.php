<?php
/** 
 * Copyright (c) 2006-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');
require_once('models/ecommerce/ecommerce_product_taxonomy.php');

class Onyx_Controller_Node_Content_Product_Highlights extends Onyx_Controller_Node_Content_Default {

    /**
     * main action
     */

    public function mainAction() {
        
        /**
         * initialize node
         */
         
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        $node_data = $Node->nodeDetail($this->GET['id']);
        $taxonomy_ids = $Node->getTaxonomyForNode($node_data['id']);
        
        if (is_array($node_data['component']['related']) || count($taxonomy_ids) > 0) {
                        
            /**
             * prepare HTTP query for product_list component
             */
             
            $product_id_list = array();
            

            if (count($node_data['component']['related']) > 0) {
                foreach ($node_data['component']['related'] as $item) {
                    if (is_numeric($item)) $product_id_list[] = $item;
                }
            }

            /**
             * Get product id list by category if catogory specified
             */
            $this->extentProductIdListByTaxonomy($taxonomy_ids, $product_id_list);

            /**
             * build query
             */
             
            $query_raw = array();
            
            // sorting
            if ($node_data['component']['display_sorting'] == 1) $query_raw['display_sorting'] = 1;
            
            // force sorting as listed
            $query_raw['product_id_list_force_sorting_as_listed'] = 1;
            
            // image role
            if ($node_data['component']['image_role']) $query_raw['image_role'] = $node_data['component']['image_role'];

            // image width
            if ($node_data['component']['image_width'] > 0) $query_raw['image_width'] = $node_data['component']['image_width'];
            
            /**
             * product_id_list
             */
             
            $query_raw['product_id_list'] = $product_id_list;
            
            //dont continue if list is empty
            if (count($query_raw['product_id_list']) == 0) return true;
            
            $query = http_build_query($query_raw, '', ':');
            
            /**
             * detect controller for product list
             */
    
            switch ($node_data['component']['template']) {
            
                case 'stack':
                    $controller = 'product_list_stack';
                    break;
                case 'scroll':
                    $controller = 'product_list_scroll';
                    break;
                case '6col':
                    $controller = 'product_list_6columns';
                    break;
                case '5col':
                    $controller = 'product_list_5columns';
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
             * call controller
             */
            $_Onyx_Request = new Onyx_Request("component/ecommerce/$controller~{$query}~");
            $this->tpl->assign('PRODUCT_LIST', $_Onyx_Request->getContent());
            
        }
        
        $this->tpl->assign('NODE', $node_data);
        
        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }

    /**
     * extentProductIdListByTaxonomy
     */

    function extentProductIdListByTaxonomy($taxonomy_ids, &$product_id_list)
    {
        if (is_array($taxonomy_ids) && count($taxonomy_ids) > 0) {

            $Taxonomy = new ecommerce_product_taxonomy();

            $ids = implode(",", $taxonomy_ids);
            $products = $Taxonomy->listing("taxonomy_tree_id IN ($ids)");

            foreach ($products as $product) {
                if (!in_array($product['node_id'], $product_id_list)) {
                    $product_id_list[] = $product['node_id'];
                }
            }
        }
    }

}
