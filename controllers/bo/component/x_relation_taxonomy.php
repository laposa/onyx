<?php
/** 
 * Copyright (c) 2006-2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * split into: taxonomy_manager, taxonomy_filter - can be used in FE
 * taxonomy_manager_node, taxonomy_manager_product
 */

require_once('controllers/bo/component.php');
require_once('models/common/common_node.php');

class Onyx_Controller_Bo_Component_X_Relation_Taxonomy extends Onyx_Controller_Bo_Component {

    /**
     * main action
     */
     
    public function mainAction() {

        parent::assignNodeData();

        /**
         * initialise
         */

        $template = (isset($_GET['edit']) && $_GET['edit'] == 'true') ? 'edit' : 'preview';
        
        switch ($this->GET['relation']) {
            case 'product':
                require_once('models/ecommerce/ecommerce_product_taxonomy.php');
                $Taxonomy = new ecommerce_product_taxonomy();
            break;
            case 'variety':
                require_once('models/ecommerce/ecommerce_product_variety_taxonomy.php');
                $Taxonomy = new ecommerce_product_variety_taxonomy();
            break;
            case 'recipe':
                require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');
                $Taxonomy = new ecommerce_recipe_taxonomy();
            break;
            case 'store':
                require_once('models/ecommerce/ecommerce_store_taxonomy.php');
                $Taxonomy = new ecommerce_store_taxonomy();
            break;
            case 'customer':
                require_once('models/client/client_customer_taxonomy.php');
                $Taxonomy = new client_customer_taxonomy();
            break;
            
            case 'node':
            default:
                require_once('models/common/common_node_taxonomy.php');
                $Taxonomy = new common_node_taxonomy();
            break;
        }
        
        /**
         * saving
         */

        
        if (isset($_POST['relation_taxonomy']) && is_array($_POST['relation_taxonomy']) && is_numeric($this->node_data['id'])) {

            $current = array();
            $submitted = array();

            // prepare list of ids currently in the database
            $current_raw = $Taxonomy->listing("node_id = " . $this->node_data['id']);
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
                    $Taxonomy->delete($id);
                    msg("Relation to the category $taxonomy_tree_id has been removed.", 'ok', 1);
                }
            }

            // insert items which were submitted and not in the database yet
            foreach ($submitted as $taxonomy_tree_id) {
                if (!isset($current[$taxonomy_tree_id])) {
                    if ($Taxonomy->insert(array('node_id' => $this->node_data['id'], 'taxonomy_tree_id' => $taxonomy_tree_id))) {
                        msg("Relation to the category $taxonomy_tree_id has been added.", 'ok', 1);
                    }
                }
            }

        }
        
        /**
         * listing
         */
         
        if (is_numeric($this->node_data['id'])) {
            $current = $Taxonomy->listing("node_id = " . $this->node_data['id']);
        
            if (is_array($current)) { 
                foreach ($current as $c) {
                    $taxonomy_data = $Taxonomy->getLabel($c['taxonomy_tree_id']);
                    //print_r($taxonomy_data);
                    //check, if there is product_list_container ??
                    $this->tpl->assign("CURRENT", $taxonomy_data);
                    $_Onyx_Request = new Onyx_Request("component/breadcrumb_taxonomy~id={$taxonomy_data['id']}~");
                    $this->tpl->assign('BREADCRUMB', $_Onyx_Request->getContent());
                    $this->tpl->parse("content.{$template}.ptn");
                }
            }
        }

        parent::parseTemplate();

        return true;
    }
}
