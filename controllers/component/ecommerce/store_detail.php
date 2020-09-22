<?php
/**
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/ecommerce/ecommerce_store.php');
require_once('models/ecommerce/ecommerce_store_taxonomy.php');
require_once('models/common/common_taxonomy_tree.php');

class Onyx_Controller_Component_Ecommerce_Store_Detail extends Onyx_Controller {

    /**
     * main action
     */
    public function mainAction()
    {
        $Store = new ecommerce_store();
        $Taxonomy_Tree = new common_taxonomy_tree();

        $node_id = (int) $this->GET['node_id'];
        $store = $Store->findStoreByNode($node_id);
        $taxonomy = $Taxonomy_Tree->getRelatedTaxonomy($store['id'], "ecommerce_store_taxonomy");

        if ($store) {

            /**
             * get store taxonomy print out
             */
             
            $_Onyx_Request = new Onyx_Request("component/ecommerce/store_taxonomy~store_id={$store['id']}~");
            $this->tpl->assign("STORE_TAXONOMY", $_Onyx_Request->getContent());

            /**
             * text depending if my selected store
             */
             
            if ($_SESSION['client']['customer']['store_id'] == $store['id']) $this->tpl->assign('MY_SELECTED_STORE', 'My selected store');
            else $this->tpl->assign('MY_SELECTED_STORE', 'Save as my own store');
            
            /**
             * assign and parse
             */
             
            $this->tpl->assign("STORE", $store);
            $this->tpl->parse("content.store");

        } else {

            $this->tpl->parse("content.no_store");

        }

        return true;
    }
}

