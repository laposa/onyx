<?php
/**
 * Copyright (c) 2013-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/ecommerce/ecommerce_store.php');
require_once('models/ecommerce/ecommerce_store_taxonomy.php');
require_once('models/common/common_node.php');
require_once('models/common/common_node_taxonomy.php');
require_once('models/common/common_uri_mapping.php');
require_once('models/client/client_customer.php');

class Onyx_Controller_Component_Ecommerce_Store_Locator extends Onyx_Controller {

    public ecommerce_store $Store;
    /**
     * main action
     */
    public function mainAction()
    {
        // init URI mapping object and store object
        $Mapping = new common_uri_mapping();
        $this->Store = new ecommerce_store();
        
        // get selected store for detail
        $node_id = (int) $this->GET['node_id'];

        // load stores, store pages and related categories
        $store_pages = $this->getStorePages();
        $stores = $this->getAllStores($this->GET['store_type_id'] ?? null);
        $categories = $this->getAllStoreTaxonomyIds();
        $selected_store = $this->getStoreAssociatedToNode($node_id);
        $page_categories = $this->getPageTaxonomyIds($node_id);

        // process request to save store as my own store
        if (isset($this->GET['set_home_store']) && $this->GET['set_home_store'] == 'true' && $selected_store['id'] > 0) {
            if ($this->updateCustomersHomeStore($selected_store['id'])) msg("Your store has been updated.");
            else msg("Please login into your account to save your store.");
            return true;
        }

        // init map bounds
        $bounds['latitude']['max'] = -9999;
        $bounds['latitude']['min'] = 9999;
        $bounds['longitude']['max'] = -9999;
        $bounds['longitude']['min'] = 9999;

        // display pins
        foreach ($stores as $store) {
            
            if ($store['latitude'] != 0 && $store['longitude'] != 0) {

                // find page and url
                $page = $store_pages[$store['id']] ?? [];
                if (empty($page['id'])) $page['id'] = 5; // if store doesn't have homepage, send to site homepage
                $store['url'] = $Mapping->stringToSeoUrl("/page/{$page['id']}");
                $store['node_id'] = $page['id'];
                $store['icon'] = $store['id'] == ($selected_store['id'] ?? null) ? 'false' : 'true';
                $store['open'] = $store['id'] == ($selected_store['id'] ?? null) ? 'true' : 'false';

                $session_store_id = $_SESSION['client']['customer']['store_id'] ?? null;
                if ($store['id'] == $session_store_id) $store['icon'] = 'false';
                
                // adjust bounds (by province/county)
                if (is_array($categories[$store['id']]) && array_intersect($page_categories, $categories[$store['id']])) {
                    if ($store['latitude'] > $bounds['latitude']['max']) $bounds['latitude']['max'] = $store['latitude'];
                    if ($store['latitude'] < $bounds['latitude']['min']) $bounds['latitude']['min'] = $store['latitude'];
                    if ($store['longitude'] > $bounds['longitude']['max']) $bounds['longitude']['max'] = $store['longitude'];
                    if ($store['longitude'] < $bounds['longitude']['min']) $bounds['longitude']['min'] = $store['longitude'];
                }

                $store['street_view_options'] = unserialize($store['street_view_options']);

                switch ($store['street_view_options']['image'] ?? null) {
                    case 1:
                        $lat = $store['street_view_options']['latitude'];
                        $lng = $store['street_view_options']['longitude'];
                        if (empty($lat)) $lat = $store['latitude'];
                        if (empty($lng)) $lng = $store['longitude'];
                        $store['image'] = 'http://maps.googleapis.com/maps/api/streetview?size=130x130'
                            . '&location=' . $lat . "," . $lng
                            . '&fov=' . ((int) $store['street_view_options']['fov'])
                            . '&heading=' . ((int) $store['street_view_options']['heading'])
                            . '&pitch=' . ((int) $store['street_view_options']['pitch'])
                            . '&sensor=false'
                            . '&key=' . ONYX_GOOGLE_API_KEY;
                        break;
                    case 2:
                        $store['image'] = "/thumbnail/130x130/" . $this->getStoreImage($store['id']) . '?fill=1';
                        break;
                    case 0:
                    default:
                        $store['image'] = '/image/var/files/generic_store.jpg';
                }
                
                // parse item
                $this->tpl->assign("STORE", $store);
                $this->tpl->parse("content.map.store_marker");

            }

        }

        // center map to ...
        if ($selected_store) {

            // ... to a selected store
            $map['latitude'] = $selected_store['latitude'] + 0.004;
            $map['longitude'] = $selected_store['longitude'];

        } else {

            // ... to bounds of a selected region (province/county)
            if ($bounds['latitude']['min'] != 9999) {
                $this->tpl->assign("BOUNDS", $bounds);
                $this->tpl->parse("content.map.fit_to_bounds");
            }
            $map['latitude'] = $this->Store->conf['latitude'];
            $map['longitude'] = $this->Store->conf['longitude'];
        }

        $this->tpl->assign("NODE_ID", $node_id);
        $this->tpl->assign("MAP", $map);
        $this->tpl->parse("content.map");

        return true;
    }



    /**
     * Returns array of all store pages. Store id is used as array index.
     * 
     * @return Array
     */
    protected function getStorePages()
    {

        $Node = new common_node();

        $pages_raw = $Node->listing("node_group = 'page' AND node_controller = 'store' AND content ~ '[0-9]+'");

        $pages = array();

        foreach ($pages_raw as $page) {
            $store_id = (int) $page['content'];
            $pages[$store_id] = $page;
        }

        return $pages;
    }


    /**
     * Returns array of all published stores in the database
     * 
     * @return Array
     */
    protected function getAllStores($type_id)
    {
        if (!is_numeric($type_id)) $type_id = 1;
        $store_list = $this->Store->listing("publish = 1 AND type_id = $type_id", "title ASC");
        
        // help old installations with transtion from one address field to multiple fields
        foreach ($store_list as $i=>$item) {
            if (trim($item['address']) == '') {
                if ($item['address_name']) $store_list[$i]['address'] .= $item['address_name'] . ",\n";
                if ($item['address_line_1']) $store_list[$i]['address'] .= $item['address_line_1'] . ",\n";
                if ($item['address_line_2']) $store_list[$i]['address'] .= $item['address_line_2'] . ",\n";
                if ($item['address_line_3']) $store_list[$i]['address'] .= $item['address_line_3'] . ",\n";
                if ($item['address_city']) $store_list[$i]['address'] .= $item['address_city'] . ",\n";
                if ($item['address_county']) $store_list[$i]['address'] .= $item['address_county'] . ",\n";
                if ($item['address_post_code']) $store_list[$i]['address'] .= $item['address_post_code'] . ",\n";
                
                $store_list[$i]['address'] = preg_replace("/,$/", "", $store_list[$i]['address']);
            }
        }
        
        return $store_list;
    }


    /**
     * Returns array of all published stores in the database
     * 
     * @return Array
     */
    protected function getStoreImage($store_id)
    {
        return $this->Store->getStoreImage($store_id);
    }



    /**
     * Returns two dimensional arrays of store categories. 
     * Result array has the following structure:
     *
     * array(
     *    store_id => array( taxonomy_id, taxonomy_id, ...)
     *    store_id => array( taxonomy_id, taxonomy_id, ...)
     *    store_id => array( taxonomy_id, taxonomy_id, ...)
     *    ...
     * )
     *
     * I.e. list of categories associated to the store can
     * be accesed using $categories[$store_id]
     * 
     * @return Array
     */
    protected function getAllStoreTaxonomyIds()
    {
        $Store_Taxonomy = new ecommerce_store_taxonomy();

        $categories_raw = $Store_Taxonomy->listing();

        $categories = array();

        // allow access by store_id
        foreach ($categories_raw as $category) {
            $categories[$category['node_id']][] = $category['taxonomy_tree_id'];
        }

        return $categories;
    }



    /**
     * Returns store associated to given node
     * 
     * @param  int    $node_id Store page node_id
     * @return Array
     */
    protected function getStoreAssociatedToNode($node_id)
    {
        return $this->Store->findStoreByNode($node_id);
    }



    /**
     * Return array of taxonomy_ids associated to given node
     * 
     * @param  int    $node_id Node id
     * @return Array
     */
    protected function getPageTaxonomyIds($node_id)
    {
        $Node_Taxonomy = new common_node_taxonomy();

        $page_categories_raw = $Node_Taxonomy->listing("node_id = $node_id");

        $page_categories = array();

        foreach ($page_categories_raw as $category) {
            $page_categories[] = $category['taxonomy_tree_id'];
        }

        return $page_categories;
    }



    /**
     * Update customer's other_data to include given home store_id
     * 
     * @param  int $store_id Store id
     */
    protected function updateCustomersHomeStore($store_id)
    {
        $customer_id = (int) $_SESSION['client']['customer']['id'];

        if ($customer_id == 0) return false;

        $Customer = new client_customer();

        // update other_data
        $_SESSION['client']['customer']['store_id'] = $store_id;

        $Customer->updateCustomer(array(
            'id' => $customer_id,
            'store_id' => $_SESSION['client']['customer']['other_data']
        ));

        return true;
    }

}

