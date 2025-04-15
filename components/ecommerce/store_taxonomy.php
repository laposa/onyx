<?php
/**
 * Copyright (c) 2013-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/ecommerce/ecommerce_store.php');
require_once('models/ecommerce/ecommerce_store_taxonomy.php');
require_once('models/common/common_taxonomy.php');

class Onyx_Controller_Component_Ecommerce_Store_Taxonomy extends Onyx_Controller {

    /**
     * main action
     */
    public function mainAction()
    {
        // initiate
        $Store = new ecommerce_store();
        $Taxonomy = new common_taxonomy();

        // input
        $store_id = $this->GET['store_id'];
        if (!is_numeric($store_id)) return false;

        // get list
        $taxonomy_list = $Taxonomy->getRelatedTaxonomy($store_id, "ecommerce_store_taxonomy");

        if (count($taxonomy_list) > 0) {
            foreach ($taxonomy_list as $category) {
                if ($category['publish'] == 1 && $category['parent'] == ONYX_STORE_FACILITY_TAXONOMY_ID) {
                    $this->tpl->assign("CATEGORY", $category);
                    $this->tpl->parse("content.category");
                }
            }
        }

        return true;
    }
}

