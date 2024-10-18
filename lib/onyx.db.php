<?php
/**
 * Onyx_Db class definition
 *
 * custom Active Record Database Pattern and simple validation
 *
 * Copyright (c) 2005-2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

use Symfony\Contracts\Cache\ItemInterface;

require_once('lib/onyx.container.php');

class Onyx_Db {
    public $conf = [];
    public $_cacheable = false; // can be overwritten by setCacheable i.e. by default in constructor
    public $_valid = [];
    public $_class_name = '';
    public $_public_attributes = [];
    public $_metaData;
    /** @var Doctrine\DBAL\Connection */
    public $db;
    /** @var Symfony\Component\Cache\Adapter\TagAwareAdapter */
    public $cache;
    /** @var Onyx_Container */
    protected $container;

    /**
     * Constructor
     */
    public function __construct() {
        $this->generic();
    }

    /**
     * create table SQL
     */
    private function createTableSql() {
        return '';
    }

    /**
     * default method called from constructor
     */
    public function generic() {

        $this->_class_name = get_class($this);
        if (defined('ONYX_DB_QUERY_CACHE')) $this->setCacheable(ONYX_DB_QUERY_CACHE);

        // Get instance of dependency injection container
        $this->container = Onyx_Container::getInstance();

        msg("{$this->_class_name}: Calling generic()", 'ok', 3);

        if ($this->container->has('onyx_db')) $this->db = $this->container->get('onyx_db');
        if ($this->container->has('onyx_db_cache')) $this->cache = $this->container->get('onyx_db_cache');

        $vars = get_object_vars($this);

        //init configuration for current model
        $this->conf = $this->initConfiguration();

        foreach ($vars as $key => $val) {
            if (!preg_match('/^_/', $key) && $key != 'db' && $key != 'id' && $key != 'conf') {
                $this->_public_attributes[$key] = $val;
            }
        }
    }

    /**
     * to be implemented in all models
     */
    static function initConfiguration() {
        return [];
    }

    /**
     * getter
     *
     * @param string $attribute
     * @return string
     */
    public function get($attribute) {
        msg("{$this->_class_name}: Calling get($attribute)", 'ok', 4);
        return $this->$attribute;
    }

    /**
     * setter
     *
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    public function set($attribute, $value) {
        // msg("{$this->_class_name}: Calling set($attribute, $value)", 'ok', 4);
        $validation_type = $this->_metaData[$attribute]['validation'];

        if ($this->_metaData[$attribute]['required'] == true || $value != '') {
            if ($this->validation($validation_type, $attribute, $value)) {
                $this->$attribute = $value;
                return true;
            } else {
                return false;
            }
        } else {
            $this->$attribute = $value;
            return true;
        }
    }

    /**
     * populate all fields
     *
     * @param array $data
     * @return boolean
     */
    public function setAll($data) {
        msg("{$this->_class_name} Calling setAll(): " . print_r($data, true), 'ok', 3);
        // reset previous validation
        $this->_valid = [];

        if (is_array($data) && count($data) > 0) {
            foreach ($this->_public_attributes as $key => $value) {
                if (key_exists($key, $data)) {
                    $this->set($key, $data[$key]);
                } elseif (is_array($this->_metaData) && key_exists($key, $this->_metaData) && $this->_metaData[$key]['required'] == true) {
                    msg("{$this->_class_name} key $key is required, but not set", 'error', ONYX_MODEL_STRICT_VALIDATION ? 1 : 2);
                    if (ONYX_MODEL_STRICT_VALIDATION) $this->setValid($key, false);
                }
            }

            return true;
        } else {
            $this->setValid('All attributes.', false);
            return false;
        }
    }

    /**
     * validation of attributes value
     *
     * @param string $validation_type
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    public function validation($validation_type, $attribute, $value) {
        switch ($validation_type) {
            // please dont' use boolean, it's not a good idea in PHP :)
            case 'boolean':
                if (is_bool($value)) {
                    $this->setValid($attribute, true);
                    return true;
                } else {
                    $this->setValid($attribute, false);
                    return false;
                }
            case 'int':
            case 'decimal':
            case 'numeric':
                if (is_numeric($value)) {
                    $this->setValid($attribute, true);
                    return true;
                } else {
                    $this->setValid($attribute, false);
                    return false;
                }
            case 'string':
            case 'text':
            case 'serialized':
            case 'xml':
                $value = trim($value);
                if ($value != '') {
                    $this->setValid($attribute, true);
                    return true;
                } else {
                    if ($this->_metaData[$attribute]['required'] == true) {
                        $this->setValid($attribute, false);
                        /*
                        ($this->_metaData[$attribute]['label'] == '') ? $label = $attribute: $label = $this->_metaData[$attribute]['label'];
                        msg("$label is required","error", 0);
                        */
                        return false;
                    }
                }
            case 'xhtml':
                //don't do any validation if Tidy is not installed
                if (!function_exists('tidy_get_status')) return true;

                //msg($_GET['request']);
                //msg($value);
                $tidy_content = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>test</title></head><body>' . $value . '</body></html>';
                // Specify configuration
                $config = [
                    'show-warnings'       => true,
                    'doctype'             => 'transitional',
                    'indent'              => true,
                    'new-blocklevel-tags' => 'article aside audio bdi canvas details dialog figcaption figure footer header hgroup main menu menuitem nav section source summary template track video',
                    'new-empty-tags'      => 'command embed keygen source track wbr',
                    'new-inline-tags'     => 'audio command datalist embed keygen mark menuitem meter output progress source time video wbr',
                    'output-xhtml'        => true,
                    'wrap'                => 200];

                // Tidy
                $tidy = new tidy;
                $tidy->parseString($tidy_content, $config, 'utf8');
                //$tidy->cleanRepair();
                //$tidy->diagnose();

                // get result
                $result_status = tidy_get_status($tidy);
                $result_message = tidy_get_error_buffer($tidy);

                if ($result_status > 1) {
                    $error = $result_message;
                } elseif ($result_status > 0) {
                    msg("Tidy warning: $result_message", "error", 2);
                }

                if (isset($error)) {
                    msg($error, 'error');
                    $this->setValid($attribute, false);
                    return false;
                } else {
                    $this->setValid($attribute, true);
                    return true;
                }
            case 'datetime':
                //$this->setValid($attribute, true);
                return true;
            case 'date': // ISO date
                $regex = "/^\d{4}-\d{1,2}-\d{1,2}$/";
                if (preg_match($regex, $value, $matches)) {
                    $this->setValid($attribute, true);
                    return true;
                } else {
                    $this->setValid($attribute, false);
                    return false;
                }
            case 'email':
                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->setValid($attribute, true);
                    return true;
                } else {
                    msg(I18N_ERROR_ENTER_VALID_EMAIL, 'error');
                    $this->setValid($attribute, false);
                    return false;
                }
            case 'url':
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->setValid($attribute, true);
                    return true;
                } else {
                    msg(I18N_ERROR_WRONG_URL, "error", 2);
                    $this->setValid($attribute, false);
                    return false;
                }
            case 'product_code':
                // be aware of "_", in SQL LIKE (escape it, or don't use it)
                if (preg_match('/^[0-9a-zA-Z-]*$/', $pc)) {
                    $this->setValid($attribute, true);
                    return true;
                } else {
                    msg(I18N_ERROR_INVALID_PRODUCT_CODE, 'error', 2);
                    $this->setValid($attribute, false);
                    return false;
                }
            default:
                $this->setValid($attribute, true);
                return true;
        }
    }

    /**
     * check if all attributes have valid values
     *
     * @return boolean
     */
    public function getValid() {
        msg('Onyx_Model.getValid', 'ok', 2);
        //todo: add checking if are required fields filled in
        $notvalid = 0;

        foreach ($this->_valid as $rec) {
            if ($rec[1] == false) {
                ($this->_metaData[$rec[0]]['label'] == '') ? $label = $rec[0] : $label = $this->_metaData[$rec[0]]['label'];
                msg(I18N_ERROR_INVALID_VALUE_FOR . $label, 'error', 1);
                $notvalid = 1;
            }
        }

        return $notvalid == 0;
    }

    /**
     * mark attribute validation status
     *
     * @param string $attribute
     * @param boolean $bool
     */
    public function setValid($attribute, $bool) {
        $this->_valid[] = [$attribute, $bool];
    }

    /**
     * listing records
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     * @return array|false
     */
    public function listing($where = '', $order = 'id ASC', $limit = '') {
        msg("{$this->_class_name}: Calling listing($where, $order, $limit)", 'ok', 3);

        if ($where != '') $where = " WHERE $where";
        if ($order != '') $order = " ORDER BY $order";

        // prepare limit query
        if (preg_match('/[0-9]*,[0-9]*/', $limit)) {
            $limit = explode(',', $limit);
            $limit = " LIMIT {$limit[1]} OFFSET {$limit[0]}";
        } else if (is_numeric($limit)) {
            $limit = " LIMIT $limit";
        } else {
            $limit = '';
        }

        //  execute query
        $sql = "SELECT * FROM {$this->_class_name} $where $order $limit";

        if ($this->isCacheable()) $records = $this->executeSqlCached($sql);
        else $records = $this->executeSql($sql);

        return is_array($records) ? $records : false;
    }

    /**
     * detail of a record
     *
     * @param int $id
     * @return array|false
     */

    public function detail($id) {
        msg("{$this->_class_name}: Calling detail($id)", 'ok', 3);

        if (!is_numeric($id)) {
            msg("Onyx_Model.detail(): id of {$this->_class_name} is not numeric", 'error', 1);
            return false;
        }

        // prepare & execute query
        $sql = "SELECT * FROM {$this->_class_name} WHERE id={$id}";
        if ($this->isCacheable()) $records = $this->executeSqlCached($sql);
        else $records = $this->executeSql($sql);

        $records = $records[0] ?? null;
        if (is_array($records)) {
            $this->setAll($records);
            return $records;
        } else {
            msg("Onyx_Model.detail(): record id=$id does not exists in {$this->_class_name}", 'error', 1);
            return false;
        }
    }

    /**
     * count records
     *
     * @param string $where
     * @return array|false
     */
    public function count($where = '') {
        msg("{$this->_class_name}: Calling count($where)", 'ok', 3);
        if ($where != '') $where = " WHERE $where";

        $sql = "SELECT count(*) AS count FROM {$this->_class_name} $where";

        if ($this->isCacheable()) $records = $this->executeSqlCached($sql);
        else $records = $this->executeSql($sql);

        if (is_array($records)) {
            return $records[0]['count'];
        } else {
            return false;
        }
    }

    /**
     * insert a record
     *
     * @param array $data
     * @return integer
     */
    public function insert($data) {
        msg("{$this->_class_name}: Calling insert() " . print_r($data, true), 'ok', 3);
        if (is_array($data)) $this->setAll($data);
        if (!$this->getValid()) {
            msg("Insert data to {$this->_class_name} is not valid", 'error');
            return false;
        }

        try {
            $this->db->insert($this->_class_name, $data);

            if (is_numeric($data['id'] ?? null)) {
                $id = $data['id'];
            } else {
                $id = $this->db->lastInsertId();
            }

            msg("Inserting of record id:{$id} into {$this->_class_name} has been successful.", 'ok', 2);
            return $id;
        } catch (Exception $e) {
            msg("Insert(" . print_r($data, true) . ") to {$this->_class_name} failed: " . print_r($e->getMessage(), true), 'error', 1);
            return false;
        }
    }

    /**
     * update a record
     *
     * @param array $data
     * @return integer
     */

    public function update($data) {
        msg("{$this->_class_name}: Calling update() " . print_r($data, true), 'ok', 3);

        if (!is_numeric($data['id'])) {
            msg("Onyx_Model.update: {$this->_class_name} id is not numeric", 'error');
            return false;
        }

        if (is_array($data)) $this->setAll($data);
        if (!$this->getValid()) {
            msg("Onyx_Model.update: {$this->_class_name} data are not valid", 'error');
            return false;
        }

        try {
            $this->db->update($this->_class_name, $data, ['id' => $data['id']]);
            msg("Record id:{$this->id} in {$this->_class_name} has been successfully updated.", 'ok', 2);
            return $data['id'];
        } catch (Exception $e) {
            msg($e->getMessage(), 'error', 1);
            msg("Failed to update {$this->_class_name}: " . print_r($e->getMessage(), true), 'error', 1);
            return false;
        }
    }

    /**
     * Save a record
     * update or insert
     *
     * @return integer
     */
    public function save($data) {
        if (is_numeric($data['id'])) {
            return $this->update($data);
        } else {
            $id = $this->insert($data);
            return $id;
        }
    }

    /**
     * Delete a record
     *
     * @param int $id
     * @return boolean
     */
    public function delete($id) {
        msg(get_class($this) . ": Calling delete($id)", 'ok', 3);

        if (!is_numeric($id)) {
            msg('Missing ID', 'error');
            return false;
        }

        try {
            $this->db->delete($this->_class_name, ['id' => $id]);
            msg("Record id:{$id} in {$this->_class_name} has been successfully deleted.", 'ok', 2);
            return true;
        } catch (Exception $e) {
            msg($e->getMessage(), 'error', 1);
            msg("Failed to delete {$this->_class_name}: " . print_r($e->getMessage(), true), 'error', 1);
            return false;
        }
    }


    /**
     * delete all records
     *
     * @return boolean
     */
    public function deleteAll($where = '') {
        msg(get_class($this) . ": Calling deleteAll($where)", 'ok', 3);
        if ($where != '') $where_statement = "WHERE $where";

        try {
            $this->db->executeQuery("DELETE FROM {$this->_class_name} $where_statement");
            msg("Everything from {$this->_class_name} has been successfully deleted.", 'ok', 2);
            return true;
        } catch (Exception $e) {
            msg($e->getMessage(), 'error', 1);
            msg("Failed to deleteAll {$this->_class_name}: " . print_r($e->getMessage(), true), 'error', 1);
            return false;
        }
    }

    /**
     * set cacheable SQL
     *
     * @param boolean $bool
     */
    public function setCacheable($bool = true) {
        $this->_cacheable = $bool;
    }

    /**
     * get status of cache option
     *
     * @return boolean
     */
    function isCacheable() {
        return $this->_cacheable;
    }

    /**
     * execute sql
     */
    public function executeSql($sql) {
        if ($this->isCacheable() && preg_match("/^SELECT/i", trim($sql))) return $this->executeSqlCached($sql);
        else return $this->executeSqlOnDatabase($sql);
    }

    /**
     * execute sql on database
     */
    public function executeSqlOnDatabase($sql) {
        if (!$this->validateSqlQuery($sql)) return false;

        try {
            $records = $this->db->fetchAllAssociative($sql);

            if (is_array($records)) return $records;
            else return false;
        } catch (Exception $e) {
            msg($e->getMessage(), 'error', 1);
            msg("Failed to executeSql {$this->_class_name}: " . print_r($e->getMessage(), true), 'error', 1);
            return false;
        }
    }

    /**
     * query cache
     */
    public function executeSqlCached($sql) {
        // create cache key
        $cacheId = "SQL_{$this->_class_name}_" . md5($sql);
        $records = $this->cache->get($cacheId, function (ItemInterface $item) use ($sql) {
            $item->tag($this->_class_name);
            $records = $this->executeSqlOnDatabase($sql);
            return serialize($records);
        });
        return unserialize($records);
    }

    /**
     * flush cache (for this object)
     */
    public function flushCache() {
        return $this->cache->invalidateTags($this->_class_name);
    }

    /**
     * validate SQL query
     */
    public function validateSqlQuery($sql) {
        if (trim($sql) == '' || !is_string($sql)) return false;
        else return true;
    }

    /**
     * get table size
     */
    public function getTableSize() {
        $sql = "SELECT pg_size_pretty(pg_total_relation_size('{$this->_class_name}')) AS total_size;";

        if ($result = $this->executeSql($sql)) {
            return $result[0]['total_size'];
        } else {
            return false;
        }
    }

    /**
     * get table information
     *
     * @param $tableName string|false
     * @return array|\Doctrine\DBAL\Schema\Table
     */
    public function getTableInformation($tableName = false) {
        if (!$tableName) $tableName = $this->_class_name;
        return $this->db->getSchemaManager()->listTableDetails($tableName);
    }
}
