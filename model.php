<?php
/**
 * Copyright (c) 2014-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * please note all local attribute should use "local_" prefix, i.e. client_customer.local_my_own_attribute.
 * this will prevent any conflicts with future upgrades and also helps to identify what are your local attributes
 * 
 */

require_once 'lib/onxshop.db.php';

class Onxshop_Model extends Onxshop_Db {

    /**
     * insertRevision
     * 
     */
     
    public function insertRevision($data) {
        
        require_once('models/common/common_revision.php');
        $Revision = new common_revision();
        
        $revision_data = array();
        $revision_data['node_id'] = $data['id'];
        $revision_data['object'] = $this->_class_name;
        $revision_data['content'] = $data;
        
        if ($revision_id = $Revision->insertRevision($revision_data)) {
            msg("Saved revision ID $revision_id", 'ok', 1);
            return $revision_id;
        } else {
            msg("Can't save revision for node ID {$revision_data['node_id']} on object {$revision_data['object']}", 'error');
            return false;
        }
        
    }

    public function isRevisionEnabled() {
        
        $enabled = ['common_node', 'common_configuration', 'client_customer', 'ecommerce_offer', 'ecommerce_offer_group', 'ecommerce_product', 'ecommerce_product_variety', 'ecommerce_price', 'ecommerce_recipe', 'ecommerce_offer_product_variety', 'ecommerce_offer_taxonomy'];
        
        if (in_array($this->_class_name, $enabled)) return true;
        else return false;
    
    }
    
    /**
     * insert a record
     *
     * @param array $data
     * @return integer
     */
     
    public function insert($data) {
        
        $result = parent::insert($data);
        
        if ($result && $this->isRevisionEnabled()) $this->insertRevision($data);
        
        return $result;
        
    }
    
    /**
     * update a record
     *
     * @param array $data
     * @return integer
     */
     
    public function update($data) {
        
        $result = parent::update($data);
        
        if ($result && $this->isRevisionEnabled()) $this->insertRevision($data);
        
        return $result;
        
    }
}
