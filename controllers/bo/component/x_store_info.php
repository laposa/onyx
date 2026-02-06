<?php
/** 
 * Copyright (c) 2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/x.php');
require_once('models/ecommerce/ecommerce_store_type.php');
require_once('models/ecommerce/ecommerce_store.php');
require_once('models/common/common_node.php');

class Onyx_Controller_Bo_Component_X_Store_Info extends Onyx_Controller_Bo_Component_X {

    public function mainAction() {
    
        // node details
        $node = new common_node();
        $node_data = $node->detail($this->GET['node_id']);
        
        // get store details from node content in order to avoid issues with refreshing & check whether store exists
        $store = new ecommerce_store();
        $store_data = $store->detail(is_numeric($node_data['content']) ? $node_data['content'] : ($_POST['store']['id'] ?? null));
        
        // save
        if (isset($_POST['save'])) {
            if($store->storeUpdate($_POST['store'])) {
                msg("{$store_data['title']} (id={$store_data['id']}) has been updated");
                return true;
            } else {
                msg("Cannot update node {$store_data['title']} (id={$store_data['id']})", 'error');
            }
        }

        $this->tpl->assign('NODE', $node_data);

        if ($store_data) {
            $this->tpl->assign('STORE', $store_data);
            $this->parseTypeSelect($store_data['type_id'] ?? null);
            parent::parseTemplate();
        } else $this->tpl->parse("content.missing_store");

        return true;
    }

    protected function parseTypeSelect($selected_id)
    {
        $type = new ecommerce_store_type();
        $records = $type->listing();

        if (isset($_GET['edit']) && $_GET['edit'] == 'true') {
            // parse whole select
            foreach ($records as $item) {
                if ($item['id'] == $selected_id) $item['selected'] = 'selected="selected"';
                $this->tpl->assign("ITEM", $item);
                $this->tpl->parse("content.edit.type.item");
            }
            $this->tpl->parse("content.edit.type");
        } else {
            // parse only text
            foreach ($records as $item) {
                if ($item['id'] == $selected_id) {
                    $this->tpl->assign("STORE_TYPE", $item['title']);
                    break;
                }
            }
            $this->tpl->parse("content.preview.type");
        }
    }
}   
            
