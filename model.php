<?php
/**
 * Copyright (c) 2014-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * please note all local attribute should use "local_" prefix, i.e. client_customer.local_my_own_attribute.
 * this will prevent any conflicts with future upgrades and also helps to identify what are your local attributes
 * 
 */

require_once 'lib/onyx.db.php';

class Onyx_Model extends Onyx_Db {

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
        
        $enabled = ['common_node', 'common_file', 'common_image', 'common_configuration', 'ecommerce_offer', 'ecommerce_offer_group', 'ecommerce_product', 'ecommerce_product_image', 'ecommerce_product_variety', 'ecommerce_price', 'ecommerce_recipe', 'ecommerce_recipe_image','ecommerce_offer_product_variety', 'ecommerce_offer_taxonomy', 'ecommerce_store', 'ecommerce_store_image'];
        
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
        
        if ($result && $this->isRevisionEnabled()) {
            $data['id'] = $result;
            $this->insertRevision($data);
        }

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

    /**
     * set default values for $conf array
     * optionally validates existing set values against valid values (if $valid is provided)
     * 
     * @param string $key name of the configuration value
     * @param mixed $value default value
     * @param array $valid array of possible values
     * @return void
     */
    protected static function setConfDefaults(&$conf, $key, $default = null, $valid = [])
    {
        if (empty($key)) return;
        if (!is_array($conf)) return;

        $valueNotSet = empty($conf[$key]);
        $validValueNotSet = is_array($valid) && !empty($valid) && !in_array($conf[$key], $valid);

        if ($valueNotSet || $validValueNotSet) {
            $conf[$key] = $default;
        }
    }

}
