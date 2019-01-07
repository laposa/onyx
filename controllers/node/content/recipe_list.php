<?php
/** 
 * Copyright (c) 2013-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Recipe_List extends Onxshop_Controller_Node_Content_Default {

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
        
        /**
         * detect controller for recipe list
         */

        switch ($node_data['component']['template']) {
        
            case 'shelf':
                $controller = 'recipe_list_shelf';
                break;
                
            case 'stack':
                $controller = 'recipe_list_stack';
                break;

            case '4col':
                $controller = 'recipe_list_4columns';
                break;
                
            case '3col':
            default:
                $controller = 'recipe_list_3columns';
                break;
            case '2col':
                $controller = 'recipe_list_2columns';
                break;
        }
        
        /**
         * get related categories
         */
        require_once('models/common/common_node_taxonomy.php');
        $Node_Taxonomy = new common_node_taxonomy();
        $taxonomy_ids = $Node_Taxonomy->getRelationsToNode($node_data['id']);
        $taxonomy_ids = implode(",", $taxonomy_ids);

        /**
         * sorting
         */
         
        $sort_by = $node_data['component']['sort']['by'];
        $sort_direction = $node_data['component']['sort']['direction'];

        /**
         * limit
         */
         
        if (is_numeric($this->GET['limit_from']) && is_numeric($this->GET['limit_per_page'])) {
            $limit_from = $this->GET['limit_from'];
            $limit_per_page = $this->GET['limit_per_page'];
        } else if (is_numeric($node_data['component']['limit'])) {
            $limit_from = 0;
            $limit_per_page = $node_data['component']['limit'];
        } else {
            $limit_from = '';
            $limit_per_page = '';
        }

        /**
         * pagination
         */
         
        if ($node_data['component']['pagination'] == 1) {
            $display_pagination = 1;
        } else {
            $display_pagination = 0;
        }
        
        /**
         * call controller
         */

        $_Onxshop_Request = new Onxshop_Request("component/ecommerce/$controller~taxonomy_tree_id=$taxonomy_ids:sort[by]=$sort_by:sort[direction]=$sort_direction:limit_from=$limit_from:limit_per_page=$limit_per_page:display_pagination=$display_pagination~");
        $this->tpl->assign('RECIPE_LIST', $_Onxshop_Request->getContent());
        
        $this->tpl->assign('NODE', $node_data);

        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }
}
