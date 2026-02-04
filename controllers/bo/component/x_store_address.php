<?php
/**
 * Copyright (c) 2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
require_once('controllers/bo/component/x.php');
require_once('models/ecommerce/ecommerce_store.php');

class Onyx_Controller_Bo_Component_X_Store_Address extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */

    public function mainAction() {

        // get details
        $store = new ecommerce_store();
        $store_data = $store->detail($this->GET['node_id'] ?? $_POST['store']['id'] ?? null);
        $template = (isset($_GET['edit']) && $_GET['edit'] == 'true') ? 'edit' : 'preview';

        if (!$store_data) {
            return true;
        }

        // Preview address and country
        if($template == 'preview') {
            if(is_numeric($store_data['country_id'])) {
                require_once('models/international/international_country.php');
                $country = new international_country();
                $country_data = $country->detail($store_data['country_id']);
                $this->tpl->assign('COUNTRY', $country_data['name']);
            }

            $address = '';
            $address .= $store_data['address_name'] ?? '';
            $address .= $store_data['address_line_1'] ? ', <br>' . $store_data['address_line_1'] : '';
            $address .= $store_data['address_line_2'] ? ', <br>' . $store_data['address_line_2'] : '';
            $address .= $store_data['address_line_3'] ? ', <br>' . $store_data['address_line_3'] : '';
            $address .= $store_data['address_city'] ? ', <br>' . $store_data['address_city'] : '';
            $address .= $store_data['address_county'] ? ', <br>' . $store_data['address_county'] : '';
            $address .= $store_data['address_post_code'] ? $store_data['address_post_code'] : '';
            $this->tpl->assign('ADDRESS', $address);
        }

        // save
        if (isset($_POST['save'])) {
            if($store->storeUpdate($_POST['store'])) {
                msg("{$store_data['title']} (id={$store_data['id']}) has been updated");
            } else {
                msg("Cannot update node {$store_data['title']} (id={$store_data['id']})", 'error');
            }
        }

        $this->tpl->assign('STORE', $store_data);

        parent::parseTemplate();

        return true;
    }
}
