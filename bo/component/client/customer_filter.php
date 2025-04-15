<?php
/**
 * Backoffice customer list filter
 *
 * Copyright (c) 2008-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Component_Client_Customer_Filter extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * if submitted search display save button
         */
         
        if (isset($_POST['search'])) {
            $this->parseGroups();
            $this->tpl->parse('content.form.save');
        }
        
        /**
         * Store submited data to the SESSION
         */
        
        if (isset($_POST['customer-filter'])) {
            $_SESSION['bo']['customer-filter'] = $_POST['customer-filter'];
            $_SESSION['bo']['customer-filter']['group_id'] = ''; 
        } else if (is_numeric($_SESSION['bo']['customer-filter']['group_id'])) {
            /**
             * update incase group_id selected
             */
            $group_id = $_SESSION['bo']['customer-filter']['group_id'];
            if ($group_filter = $this->getGroupFilter($group_id)) {
                $_SESSION['bo']['customer-filter'] = $group_filter;
                $_SESSION['bo']['customer-filter']['group_id'] = $group_id;
            }
        }
        
        /**
         * populate filter in case it's empty
         */
        
        if (!is_array($_SESSION['bo']['customer-filter'])) {
            $_SESSION['bo']['customer-filter'] = array();
            $_SESSION['bo']['customer-filter']['invoice_status'] = 0;
            $_SESSION['bo']['customer-filter']['account_type'] = -1;
        }
        
        /**
         * copy customer-filter to local variable
         */
         
        $customer_filter = $_SESSION['bo']['customer-filter'];
        
        /**
         * if submitted save, only process save action and don't display form (exit here)
         */
        
        if (isset($_POST['save'])) return $this->saveGroupFilter($customer_filter);
        
        /**
         * assign to template variable
         */
        
        if ($group_detail = $this->getGroupDetail($customer_filter['group_id'])) $customer_filter['group_name'] = $group_detail['name'];
        else if (trim($customer_filter['group_name']) == '') $customer_filter['group_name'] = 'Your new group name';
        
        $this->tpl->assign('CUSTOMER_FILTER', $customer_filter);
        
        /**
         * With orders and account type options
         */
        
        $this->tpl->assign("SELECTED_invoice_status_{$customer_filter['invoice_status']}", "selected='selected'");
        $this->tpl->assign("SELECTED_account_type_{$customer_filter['account_type']}", "selected='selected'");
        
        /**
         * Country list
         */
         
        require_once('models/international/international_country.php');
        $Country = new international_country();
        $countries = $Country->listing();
        
        foreach ($countries as $item) {
            if ($item['id'] == $customer_filter['country_id']) $item['selected'] = "selected='selected'";
            else $item['selected'] = '';
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse('content.form.country.item');
        }
        
        $this->tpl->parse('content.form.country');

        /**
         * product list
         */
        
        if (ONYX_ECOMMERCE) {
        
            require_once('models/ecommerce/ecommerce_product.php');
            $Product = new ecommerce_product();
        
            $product_list = $Product->listing('publish = 1', 'name ASC');
        
            if (is_array($product_list) && count($product_list) > 0) {
            
                foreach ($product_list as $item) {
                    
                    if (is_array($_SESSION['bo']['customer-filter']['product_bought'])) {
                        if (in_array($item['id'], $customer_filter['product_bought'])) $item['checked'] = "checked='checked'";
                        else $item['selected'] = '';
                    } else {
                        $item['selected'] = '';
                    }
                    
                    $this->tpl->assign('ITEM', $item);
                    $this->tpl->parse('content.form.product.item');
                }
            
                $this->tpl->parse('content.form.product');
            }
        
        }
        
        $this->tpl->parse('content.form');
        
        return true;
    }
    
    /**
     * save group filter
     */
     
    public function saveGroupFilter($filter) {
        
        if (!is_array($filter)) return false;
        
        require_once('models/client/client_group.php');
        $ClientGroup = new client_group();
        
        $data = array();
        if ($_SESSION['bo']['customer-filter-selected_group_id'] > 0) $data['id'] = $_SESSION['bo']['customer-filter-selected_group_id'];
        $data['name'] = $filter['group_name'];
        $data['search_filter'] = $filter;

        /**
         * save actual group
         */

        if ($id = $ClientGroup->saveGroup($data)) {
            msg("Customers group saved under name {$data['name']} and ID $id");
            /**
             * add customer to this group
             */
        
            $this->addCustomersToGroup($id, $_POST['customer-filter']['group_ids_remove']);

            $_SESSION['bo']['customer-filter']['group_id'] = $id;
        
        } else {
            msg("Cannot save customers group", 'error');
        }
        
        return true;
        
    }

    /**
     * get group filter
     */
     
    public function getGroupFilter($group_id) {
        
        if (!is_numeric($group_id) || $group_id < 1) return false;
        
        $group_detail = $this->getGroupDetail($group_id);

        if (is_array($group_detail['search_filter']) && count($group_detail['search_filter']) > 0) {
            return $group_detail['search_filter'];
        } else {
            return false;
        }
    }
    
    /**
     * get group detail
     */
     
    public function getGroupDetail($group_id) {
        
        if (!is_numeric($group_id) || $group_id < 1) return false;
        
        require_once('models/client/client_group.php');
        $ClientGroup = new client_group();
        
        $group_detail = $ClientGroup->getDetail($group_id);

        return $group_detail;
        
    }
    
    /**
     * add customers to group
     */
     
    public function addCustomersToGroup($group_id, $group_ids_remove) {
    
        require_once('models/client/client_group.php');
        require_once('models/client/client_customer.php');
        $ClientGroup = new client_group();
        $Customer = new client_customer();
        //force cache even for back office user
        $Customer->setCacheable(true);
        
        if ($group_filter = $this->getGroupFilter($group_id)) {
        
            $customer_list = $Customer->getClientList($group_filter);
            
            $list_count = count($customer_list);

            if ($Customer->addCustomersToGroupFromList($customer_list, $group_id, $group_ids_remove)) {
                msg("All $list_count customers were added to group ID $group_id");
                //flush cache as we are using forced cache for client_customer in backoffice
                $Customer->flushCache();
            } else {
                msg("Cannot add $list_count customers to group ID $group_id", 'error');
                return false;
            }
            
        } else {
            return false;
        }
        
    }

    /**
     * Parse groups
     */
    public function parseGroups()
    {
        require_once('models/client/client_group.php');
        $ClientGroup = new client_group();
        if ($_SESSION['bo']['customer-filter-selected_group_id'] > 0) $group_id = $_SESSION['bo']['customer-filter-selected_group_id'];
        else $group_id = 0;
        $list = $ClientGroup->listing("id <> $group_id");

        if (count($list) == 0) return;

        foreach ($list as $item) {
            $this->tpl->assign('ITEM', $item);
            if ($item['id'] == $group_id) continue;
            else $this->tpl->assign('CHECKED', '');
            $this->tpl->parse('content.form.group.item');
        }

        $this->tpl->parse('content.form.group');
    }

}
