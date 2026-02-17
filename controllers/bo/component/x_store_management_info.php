<?php
/**
 * Copyright (c) 2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
require_once('controllers/bo/component/x.php');
require_once('models/ecommerce/ecommerce_store.php');

class Onyx_Controller_Bo_Component_X_Store_Management_Info extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */

    public function mainAction() {

        // get details
        $store = new ecommerce_store();
        $store_data = $store->detail($this->GET['store_id'] ?? $_POST['store']['id'] ?? null);

        if (!$store_data) {
            return false;
        }

        // save
        if (isset($_POST['save'])) {
            if($store->storeUpdate($_POST['store'])) {
                msg("Store {$store_data['title']} (id={$store_data['id']}) has been updated");
            } else {
                msg("Cannot update store {$store_data['title']} (id={$store_data['id']})", 'error');
            }
        }

        $this->tpl->assign('STORE', $store_data);

        parent::parseTemplate();

        return true;
    }
}
