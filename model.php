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

        $this->_last_update_changed = null;

        // skip the UPDATE (and the revision) when nothing would change
        if ($this->isRevisionEnabled() && is_array($data) && is_numeric($data['id'] ?? null)) {
            $this->setAll($data);
            if ($this->getValid() && !$this->hasChanges($data)) {
                $this->_last_update_changed = false;
                msg("Record id:{$data['id']} in {$this->_class_name} has no changes, update skipped.", 'ok', 2);
                return $data['id'];
            }
        }

        $result = parent::update($data);

        if ($result) $this->_last_update_changed = true;
        if ($result && $this->isRevisionEnabled()) $this->insertRevision($data);

        return $result;

    }

    /**
     * Fields ignored when checking whether an update changes anything.
     * Models can extend this list.
     */
    public $_change_check_ignored_fields = ['id', 'modified'];

    /**
     * Result of the last update() call:
     * true = record was changed, false = skipped because nothing changed, null = failed or not called
     */
    public $_last_update_changed = null;

    /**
     * Did the last update() call actually change the record?
     *
     * @return bool
     */
    public function lastUpdateChanged() {
        return $this->_last_update_changed === true;
    }

    /**
     * Compare data to be saved with the record currently stored in the database.
     * Only keys present in $data are compared. When in doubt, reports a change.
     *
     * @param array $data
     * @return bool
     */
    public function hasChanges($data) {

        if (!is_array($data) || !is_numeric($data['id'] ?? null)) return true;

        // read directly from the database, cached data could be stale
        $id = (int) $data['id'];
        $records = $this->executeSqlOnDatabase("SELECT * FROM {$this->_class_name} WHERE id = {$id}");
        bar_dump($records, 'records from database');
        bar_dump($data, 'data to be compared');
        if (!is_array($records) || !isset($records[0])) return true;
        $current = $records[0];

        foreach ($data as $key => $value) {
            if (in_array($key, $this->_change_check_ignored_fields, true)) continue;
            if (!array_key_exists($key, $current)) return true;
            if (!self::valuesEqual($current[$key], $value)) return true;
        }

        return false;
    }

    /**
     * Loose comparison of a stored DB value with a value about to be saved
     *
     * @param mixed $stored
     * @param mixed $new
     * @return bool
     */
    protected static function valuesEqual($stored, $new) {

        // unserialized structures should have been serialized by the caller, don't guess
        if (is_array($new) || is_object($new)) return false;

        if ($stored === null) $stored = '';
        if ($new === null) $new = '';

        // booleans (PostgreSQL boolean columns come back as PHP bool)
        if (is_bool($stored) || is_bool($new)) {
            $a = is_bool($stored) ? $stored : filter_var($stored, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            $b = is_bool($new) ? $new : filter_var($new, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            return $a !== null && $a === $b;
        }

        if ((string) $stored === (string) $new) return true;

        // numbers, e.g. "1.50" vs "1.5"
        if (is_numeric($stored) && is_numeric($new)) return $stored == $new;

        // serialized PHP / JSON, e.g. other_data or custom_fields
        $a = self::decodeStructured($stored);
        $b = self::decodeStructured($new);
        if ($a !== null && $b !== null) return $a == $b;

        return false;
    }

    /**
     * Decode a serialized PHP array or a JSON array/object
     *
     * @param mixed $value
     * @return array|null
     */
    protected static function decodeStructured($value) {

        if (!is_string($value) || $value === '') return null;

        if (preg_match('/^a:\d+:\{/', $value)) {
            $decoded = @unserialize($value, ['allowed_classes' => false]);
            if (is_array($decoded)) return $decoded;
        }

        if ($value[0] === '{' || $value[0] === '[') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) return $decoded;
        }

        return null;
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
        $validValueNotSet = is_array($valid) && !empty($valid) && !in_array($conf[$key] ?? [], $valid);

        if ($valueNotSet || $validValueNotSet) {
            $conf[$key] = $default;
        }
    }

}
