<?php
/** 
 * Copyright (c) 2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * split into: taxonomy_manager, taxonomy_filter - can be used in FE
 * taxonomy_manager_node, taxonomy_manager_product
 */

require_once('controllers/bo/component/x.php');
require_once('models/ecommerce/ecommerce_store_taxonomy.php');
require_once('models/ecommerce/ecommerce_store.php');

class Onyx_Controller_Bo_Component_X_Store_Taxonomy extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */
     
    public function mainAction() {

        /**
         * get details
         */
        
        $store = new ecommerce_store();
        $store_data = $store->detail($this->GET['store_id'] ?? $_POST['store']['id']);

        if (!$store_data) {
            msg("Store ID not found.", 'error');
            return false;
        }

        /**
         * initialise
         */

        $template = (isset($_GET['edit']) && $_GET['edit'] == 'true') ? 'edit' : 'preview';
        $taxonomy = new ecommerce_store_taxonomy();
        
        /**
         * saving
         */
        
        if (
            isset($_POST['save']) &&
            isset($_POST['relation_taxonomy']) && 
            is_array($_POST['relation_taxonomy']) && 
            is_numeric($_POST['store']['id'])
        ) {

            var_dump('here alright', $_POST['store']['id']);
            $current = array();
            $submitted = array();

            // prepare list of ids currently in the database
            $current_raw = $taxonomy->listing("node_id = " . $_POST['store']['id']);
            if (is_array($current_raw)) {
                foreach ($current_raw as $c) {
                    $current[$c['taxonomy_tree_id']] = $c['id'];
                }
            }

            // prepare list if ids submitted by client
            foreach ($_POST['relation_taxonomy'] as $taxonomy_tree_id) {
                if (is_numeric($taxonomy_tree_id)) $submitted[] = $taxonomy_tree_id;
            }

            // delete items which were not submitted
            foreach ($current as $taxonomy_tree_id => $id) {
                if (!in_array($taxonomy_tree_id, $submitted)) {
                    $taxonomy->delete($id);
                    msg("Relation to the category $taxonomy_tree_id has been removed.", 'ok', 1);
                }
            }

            // insert items which were submitted and not in the database yet
            foreach ($submitted as $taxonomy_tree_id) {
                if (!isset($current[$taxonomy_tree_id])) {
                    if ($taxonomy->insert(array('node_id' => $_POST['product']['id'], 'taxonomy_tree_id' => $taxonomy_tree_id))) {
                        msg("Relation to the category $taxonomy_tree_id has been added.", 'ok', 1);
                    }
                }
            }

        }
        
        /**
         * listing
         */

        if (is_numeric($store_data['id'])) {
            $current = $taxonomy->listing("node_id = " . $store_data['id']);

            if (is_array($current)) {
                foreach ($current as $c) {
                    $taxonomy_data = $taxonomy->getLabel($c['taxonomy_tree_id']);

                    $this->tpl->assign("CURRENT", $taxonomy_data);
                    $_Onyx_Request = new Onyx_Request("component/breadcrumb_taxonomy~id={$taxonomy_data['id']}~");
                    $this->tpl->assign('BREADCRUMB', $_Onyx_Request->getContent());
                    $this->tpl->parse("content.{$template}.ptn");
                }
            }

            if(!$current || count($current) == 0) {
                $this->tpl->parse("content.{$template}.empty");
            }
        }

        $this->tpl->assign('STORE', $store_data);

        parent::parseTemplate();

        return true;
    }
}
