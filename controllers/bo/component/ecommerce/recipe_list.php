<?php
/**
 *
 * Copyright (c) 2013-2020 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Ecommerce_Recipe_List extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        // initialize filter variables
        $taxonomy_id = $this->GET['taxonomy_tree_id'];
        if (isset($_POST['recipe-list-filter'])) $_SESSION['bo']['recipe-list-filter'] = $_POST['recipe-list-filter'];
        $keyword = $_SESSION['bo']['recipe-list-filter']['keyword'];

        // initialize sorting variables
        if ($this->GET['recipe-list-sort-by']) $_SESSION['bo']['recipe-list-sort-by'] = $this->GET['recipe-list-sort-by'];
        if ($this->GET['recipe-list-sort-direction']) $_SESSION['bo']['recipe-list-sort-direction'] = $this->GET['recipe-list-sort-direction'];

        if ($_SESSION['bo']['recipe-list-sort-by']) $order_by = $_SESSION['bo']['recipe-list-sort-by'];
        else $order_by = 'modified';
        if ($_SESSION['bo']['recipe-list-sort-direction']) $order_dir = $_SESSION['bo']['recipe-list-sort-direction'];
        else $order_dir = 'DESC';
        
        // initialize pagination variables
        if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
        else $from = 0;
        if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
        else $per_page = 25;

        // get the list
        require_once('models/ecommerce/ecommerce_recipe.php');
        $Recipe = new ecommerce_recipe();   
        $recipe_list = $Recipe->getFilteredRecipeList($keyword, false, $taxonomy_id, false, $per_page, $from, $order_by, $order_dir, 0);
        $count = $Recipe->getFilteredRecipeCount($keyword, false, $taxonomy_id, false, 0);
        
        if (!is_array($recipe_list)) return false;

        if (count($recipe_list) == 0) {
            $this->tpl->parse('content.empty_list');
            return true;
        }

        // display pagination
        $request = new Onyx_Request("component/pagination~link=/request/bo/component/ecommerce/recipe_list:limit_from=$from:limit_per_page=$per_page:count=$count~");
        $this->tpl->assign('PAGINATION', $request->getContent());

        // parse items
        foreach ($recipe_list as $item) {

            $item['modified'] = date("d/m/Y H:i", strtotime($item['modified']));
            $this->tpl->assign('ITEM', $item);
            if ($item['image']['src']) $this->tpl->parse('content.list.item.image');
            
            $this->tpl->assign('CLASS', $item['publish'] == 0 ? 'class="publish_0"' : "");

            $this->tpl->parse('content.list.item');
        }
        
        $this->tpl->parse('content.list');

        return true;
        
    }
}
