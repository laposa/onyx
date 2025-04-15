<?php
/**
 * Backoffice store list controller
 *
 * Copyright (c) 2013-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Ecommerce_Store_List extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        // initialize filter variables
        $taxonomy_id = $this->GET['taxonomy_tree_id'] ?? null;
        if (isset($_POST['store-list-filter'])) $_SESSION['bo']['store-list-filter'] = $_POST['store-list-filter'];
        $keyword = $_SESSION['bo']['store-list-filter']['keyword'] ?? '';
        $type_id = $_SESSION['bo']['store-list-filter']['type_id'] ?? '';

        // initialize sorting variables
        $_SESSION['bo']['store-list-sort-by'] = $this->GET['store-list-sort-by'] ?? '';
        $_SESSION['bo']['store-list-sort-direction'] = $this->GET['store-list-sort-direction'] ?? '';

        if ($_SESSION['bo']['store-list-sort-by']) $order_by = $_SESSION['bo']['store-list-sort-by'];
        else $order_by = 'modified';
        if ($_SESSION['bo']['store-list-sort-direction']) $order_dir = $_SESSION['bo']['store-list-sort-direction'];
        else $order_dir = 'DESC';

        // initialize pagination variables
        if  (is_numeric($this->GET['limit_from'] ?? null)) $from = $this->GET['limit_from'];
        else $from = 0;
        if (is_numeric($this->GET['limit_per_page'] ?? null)) $per_page = $this->GET['limit_per_page'];
        else $per_page = 25;

        // get the list
        require_once('models/ecommerce/ecommerce_store.php');
        $Store = new ecommerce_store(); 
        $store_list = $Store->getFilteredStoreList($taxonomy_id, $keyword, $type_id, $order_by, $order_dir, $per_page, $from);
        $count = $Store->getFilteredStoreCount($taxonomy_id, $keyword, $type_id);
        
        if (!is_array($store_list)) return false;

        if (count($store_list) == 0) {
            $this->tpl->parse('content.empty_list');
            return true;
        }

        // display pagination
        $_Onyx_Request = new Onyx_Request("component/pagination~link=/request/bo/component/ecommerce/store_list:limit_from=$from:limit_per_page=$per_page:count=$count~");
        $this->tpl->assign('PAGINATION', $_Onyx_Request->getContent());

        // parse items
        $even_odd = '';
        foreach ($store_list as $item) {

            $item['modified'] = date("d/m/Y H:i", strtotime($item['modified']));
            $this->tpl->assign('ITEM', $item);
            if (isset($item['image_src']) && $item['image_src']) $this->tpl->parse('content.list.item.image');
            
            $even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';
            $publish = $item['publish'] ? '' : 'disabled';
            $this->tpl->assign('CLASS', "class='$even_odd $publish fullstore'");

            $this->tpl->parse('content.list.item');
        }
        
        $this->tpl->parse('content.list');

        return true;
        
    }
}
