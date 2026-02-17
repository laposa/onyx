<?php
/** 
 * Copyright (c) 2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


require_once('controllers/bo/component/x.php');
require_once('models/common/common_node.php');
require_once('models/ecommerce/ecommerce_store.php');

class Onyx_Controller_Bo_Component_X_Store_Street_View extends Onyx_Controller_Bo_Component_X {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        $store = new ecommerce_store();

        $store_data = $store->detail($_GET['store_id'] ?? $_POST['store']['id'] ?? null);
        
        if (!$store_data) {
            return false;
        }

        $store_data['street_view_options'] = unserialize($store_data['street_view_options']);

        $google_street_view_url = 'https://maps.googleapis.com/maps/api/streetview?size=200x200';
        $google_street_view_url .= '&location=' . $store_data['latitude'] . ',' . $store_data['longitude'];
        $google_street_view_url .= '&fov=' . ($store_data['street_view_options']['fov'] ?? 90);
        $google_street_view_url .= '&heading=' . ($store_data['street_view_options']['heading'] ?? 0);
        $google_street_view_url .= '&pitch=' . ($store_data['street_view_options']['pitch'] ?? 0);
        $google_street_view_url .= '&sensor=false';
        $google_street_view_url .= '&key=' . ONYX_GOOGLE_API_KEY;

        if ($_POST['save'] ?? false) {

            $_POST['store']['street_view_options'] = serialize($_POST['store']['street_view_options']);

            if($store->storeUpdate($_POST['store'])) {
                msg("Store {$store_data['title']} (id={$store_data['id']}) has been updated");
            } else {
                msg("Cannot update store {$store_data['title']} (id={$store_data['id']})", 'error');
            }
        }

        $this->tpl->assign('STORE', $store_data);
        $this->tpl->assign('STREET_VIEW_URL', $google_street_view_url);
        $this->tpl->assign('STREET_VIEW_IMAGE_' . ($store_data['street_view_options']['image'] ?? ''), 'selected');

        parent::parseTemplate();

        return true;
    }
}
