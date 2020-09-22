<?php
/**
 * Copyright (c) 2015-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/recipe_list.php');
require_once('models/ecommerce/ecommerce_recipe.php');

class Onyx_Controller_Component_Ecommerce_Recipe_Search extends Onyx_Controller_Component_Ecommerce_Recipe_List {

    /**
     * main action
     */
    public function mainAction()
    {
        $this->Recipe = new ecommerce_recipe();

        $this->parseCategorySelect($this->GET['course']);
        $this->tpl->assign('SELECT_COOK_' . $this->GET['time'], 'selected="selected"');

        // is there a limit?
        if  (is_numeric($this->GET['limit_from'])) $limit_from = $this->GET['limit_from'];
        else $limit_from = 0;
        if (is_numeric($this->GET['limit_per_page'])) $limit_per_page = $this->GET['limit_per_page'];
        else $limit_per_page = 25;

        // perform search
        $list = $this->performSearch($limit_from, $limit_per_page);

        if (count($list) == 2) {

            // show items
            $this->parseItems($list[0]);

            // show pagination
            if ($this->GET['display_pagination'] == 1) {
                $count = $list[1];
                $_Onyx_Request = new Onyx_Request("component/pagination~limit_from=$limit_from:limit_per_page=$limit_per_page:count=$count:option_show_all=0:passthrough_get_parameters=1~");
                $this->tpl->assign('PAGINATION', $_Onyx_Request->getContent());
                
            }

        }

        return true;
    }

    /**
     * parse category select
     */

    public function parseCategorySelect($selected_id)
    {
        
        $recipe_categories = $this->Recipe->getUsedTaxonomy();
        
        foreach ($recipe_categories as $item) {
            
            if ($item['publish'] != 1) continue;

            $item['selected'] = $item['id'] == $selected_id ? 'selected="selected"' : '';
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse('content.taxonomy_item');
        
        }
        
    }

    /**
     * performSearch
     */

    public function performSearch($limit_from, $limit_per_page)
    {
        // course - taxonomy_id
        if (is_numeric($this->GET['course'])) $taxonomy_id = $this->GET['course'];
        else $taxonomy_id = false;

        // cooking time
        if (is_numeric($this->GET['time'])) $time = $this->GET['time'];
        else $time = false;

        // keywords
        if (!empty($this->GET['keywords'])) $keywords = $this->GET['keywords'];
        else $keywords = false;

        // ingredients
        if (!empty($this->GET['ingredients'])) $keywords .= " " . $this->GET['ingredients'];
        else $product_variety_sku = false;
        
        // sku
        if (!empty($this->GET['sku'])) if (!empty($product_variety_sku)) $product_variety_sku .= "," . $this->GET['sku'];
        else $product_variety_sku = $this->GET['sku'];

        return array(
            $this->Recipe->getFilteredRecipeList($keywords, $time, $taxonomy_id, $product_variety_sku, $limit_per_page, $limit_from, false, false, true),
            $this->Recipe->getFilteredRecipeCount($keywords, $time, $taxonomy_id, $product_variety_sku, true)
        );
    }

}

