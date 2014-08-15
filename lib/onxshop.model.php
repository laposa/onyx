<?php
/**
 * Onxshop_Model class definition
 *
 * custom Active Record Database Pattern and simple validation
 *
 * Copyright (c) 2005-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once 'Zend/Cache.php';

class Onxshop_Model {

	var $conf = array();
	var $_cacheable = ONXSHOP_DB_QUERY_CACHE;
	var $_valid = array();
	var $_class_name = '';
	var $_public_attributes = array();
	var $_metaData;
	var $db;
	
	/**
	 * Constructor
	 * 
	 */
		
	function __construct() {
		$this->_class_name = get_class($this);
		$this->generic();
	}
	
    /**
     * create table SQL
     */

    private function createTableSql() {

        $sql = "";
        
        return $sql;
        
    }

	
	/**
	 * default method called from constructor
	 *
	 */
	
	public function generic() {
		
		msg("{$this->_class_name}: Calling generic()", 'ok', 3);
		
		if (Zend_Registry::isRegistered('onxshop_db')) $this->db = Zend_Registry::get('onxshop_db');

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
	
		$conf = array();
	
		return $conf;
	
	}
	
	/**
	 * getter
	 *
	 * @param string $attribute
	 * @return string
	 */
	
	public function get($attribute) {
	
		msg("{$this->_class_name}: Calling get($attribute)", 'ok', 4);
		eval("\$value = \$this->$attribute;");
		
		return $value;
	}
	
	/**
	 * setter
	 *
	 * @param string $attribute
	 * @param string $value
	 */
	 
	public function set($attribute, $value) {
	
		msg("{$this->_class_name}: Calling set($attribute, $value)", 'ok', 4);
		$validation_type = $this->_metaData[$attribute]['validation'];
		
		if ($this->_metaData[$attribute]['required'] == true || $value != '') {
			if ($this->validation($validation_type, $attribute, $value)) {
				if (eval("\$this->$attribute = \$value;") == null) return true;
			} else {
				return false;
			}
		} else {
			if (eval("\$this->$attribute = \$value;") == null) return true;
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
		$this->_valid = array();// reset previous validation
		
		if (is_array($data) && count($data) > 0) {
			
			foreach ($this->_public_attributes as $key=>$value) {
			
				if (key_exists($key, $data)) {
					$this->set($key, $data[$key]);
				} else if ($this->_metaData[$key]['required'] == true) {
					msg("{$this->_class_name} key $key is required, but not set", 'error', ONXSHOP_MODEL_STRICT_VALIDATION ? 1 : 2);
					if (ONXSHOP_MODEL_STRICT_VALIDATION) $this->setValid($key, false);
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
			/* please dont' use boolean, it's not a good idea in PHP :) */
			case 'boolean':
				if (is_bool($value)) {
        			$this->setValid($attribute, true);
        			return true;
    			} else {
    				$this->setValid($attribute, false);
        			return false;
    			}
			break;
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
			break;
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
				$config = array(
					'show-warnings' => true,
					'doctype' => 'transitional',
					'indent' => true,
					'output-xhtml' => true,
					'wrap' => 200);
				
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
				} else if ($result_status > 0) {
					msg("Tidy warning: $result_message", "error", 2);
				}
				

     			if ($error != '') {
     				msg($error, 'error');
     				$this->setValid($attribute, false);
     				return false;
     			} else {
     				$this->setValid($attribute, true);
     				return true;
     			}
    		break;
			case 'datetime':
				//$this->setValid($attribute, true);
				return true;
			break;
			case 'date': // ISO date
				$regex = "/^\d{4}-\d{1,2}-\d{1,2}$/";
				if (preg_match($regex, $value, $matches)) {
					$this->setValid($attribute, true);
					return true;
				} else {
					$this->setValid($attribute, false);
					return false;
				}
			break;
			case 'email':
				$regex = '/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,32})$/i';
				if (preg_match($regex, $value, $matches)) {
					$this->setValid($attribute, true);
					return true;
				} else {
					msg(I18N_ERROR_ENTER_VALID_EMAIL, 'error');
					$this->setValid($attribute, false);
					return false;
				}
			break;
			case 'url':
				$regex = '/^(http:\/\/|ftp:\/\/)/i';
				if (preg_match($regex, $value, $matches)) {
					$this->setValid($attribute, true);
					return true;
				} else {
					msg(I18N_ERROR_WRONG_URL, "error", 2);
					$this->setValid($attribute, false);
					return false;
				}
			break;
			case 'decimal':
				$this->setValid($attribute, true);
				return true;
			break;
            case 'product_code':
                /*
                 * be aware of "_", in SQL LIKE (escape it, or don't use it) 
                 */
                if (preg_match('/^[0-9a-zA-Z-]*$/', $pc)) {
                    $this->setValid($attribute, true);
                    return true;
                } else {
                    msg(I18N_ERROR_INVALID_PRODUCT_CODE, 'error', 2);
                    $this->setValid($attribute, false);
                    return false;
                }
            break;
			default:
				$this->setValid($attribute, true);
				return true;
			break;
		}
	}
	
	/**
	 * check if all attributes have valid values
	 *
	 * @return boolean
	 */
	 
	public function getValid() {
	
		msg('Onxshop_Model.getValid', 'ok', 2);
		//todo: add checking if are required fields filled in
		$notvalid = 0;
		
		foreach ($this->_valid as $rec) {
			if ($rec[1] == false) {
				($this->_metaData[$rec[0]]['label'] == '') ? $label = $rec[0]: $label = $this->_metaData[$rec[0]]['label'];
				msg(I18N_ERROR_INVALID_VALUE_FOR . $label, 'error', 1);
				$notvalid = 1;
			}
		}
		
		if ($notvalid == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * mark attribute validation status
	 *
	 * @param string $attribute
	 * @param boolean $bool
	 */
	 
	public function setValid($attribute, $bool) {
		$this->_valid[] = array($attribute, $bool);
	}
	
	/**
	 * listing records
	 *
	 * @param string $where
	 * @param string $order
	 * @param string $limit
	 * @return array
	 */
	 
	public function listing($where = '', $order = 'id ASC', $limit = '') {
	
		msg("{$this->_class_name}: Calling listing($where, $order, $limit)", 'ok', 3);
		
		if ($where != '') $where = " WHERE $where";
		if ($order != '') $order = " ORDER BY $order";
		
		/**
		 * prepare limit query
		 */
		 
		if (preg_match('/[0-9]*,[0-9]*/', $limit)) {
			
			$limit = explode(',', $limit);
			$limit = " LIMIT {$limit[1]} OFFSET {$limit[0]}";
			
		} else {
			
			$limit = '';
			
		}
		
		/**
		 * execute query
		 */
		
		$sql = "SELECT * FROM {$this->_class_name} $where $order $limit";
		
		if ($this->isCacheable()) $records = $this->executeSqlCached($sql);
		else $records = $this->executeSql($sql);
		
		if (is_array($records)) {
		
			return $records;
		
		} else {
			
			return false;
		
		}
	}

	/**
	 * detail of a record
	 *
	 * @param int $id
	 * @return array
	 */
	 
	public function detail($id) {
	
		msg("{$this->_class_name}: Calling detail($id)", 'ok', 3);
		
		if (!is_numeric($id)) {
			msg("Onxshop_Model.detail(): id of {$this->_class_name} is not numeric", 'error', 1);
			return false;
		}
		
		/**
		 * prepare query
		 */
		 		
		$sql = "SELECT * FROM {$this->_class_name} WHERE id={$id}";
    	
    	/**
    	 * execute
    	 */
    	 
    	if ($this->isCacheable()) $records = $this->executeSqlCached($sql);
    	else $records = $this->executeSql($sql);
    	
    	$records = $records[0];
    	
    	if (is_array($records)) {
    		$this->setAll($records);
        	return $records;
    	} else {
    		msg("Onxshop_Model.detail(): record id=$id does not exists in {$this->_class_name}", 'error', 1);
    		return false;
    	}
    	
	}
	
	/**
	 * count records
	 *
	 * @param string $where
	 * @return array
	 */
	 
	public function count($where = '') {
	
		msg("{$this->_class_name}: Calling count($where)", 'ok', 3);
		if ($where != '') $where = " WHERE $where";
		
		$sql = "SELECT count(id) AS count FROM {$this->_class_name} $where";
		
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

		if ($this->getValid()) {
			
			/**
			 * try to insert
			 */
			 
			try {
				
				$this->db->insert($this->_class_name, $data);	
				
				if (is_numeric($data['id'])) {
				
					$id = $data['id'];
				
				} else {
				
					/**
					 * PostgreSQL returns the OID from lastInsertId
					 */
					 
					if (ONXSHOP_DB_TYPE == 'pgsql') {
						$id = $this->db->lastInsertId($this->_class_name, "id");
					} else {
						$id = $this->db->lastInsertId();
					}
				}
				
				msg("Inserting of record id:{$id} into {$this->_class_name} has been successful.", 'ok', 2);

				return $id;
			
			} catch (Exception $e) {
				
				msg($e->getMessage(), 'error', 1);
				msg("Insert to {$this->_class_name} failed: " . print_r($this->db->getConnection()->errorInfo(), true), 'error', 1);
				
				return false;
			}
			
		} else {
		
			return false;
			
		}

	}

	/**
	 * update a record
	 *
	 * @param array $data
	 * @return integer
	 */
	 
	public function update($data = array()) {
		
		msg("{$this->_class_name}: Calling update() " . print_r($data, true), 'ok', 3);

		if (!is_numeric($data['id'])) {
			msg("Onxshop_Model.update: {$this->_class_name} id is not numeric", 'error');
			return false;
		}
		
		if (is_array($data)) $this->setAll($data);
    		
		if ($this->getValid()) {
			
			try {    		
				
				$ok = $this->db->update($this->_class_name, $data, "id = {$data['id']}");
				msg("Record id:{$this->id} in {$this->_class_name} has been successfully updated.", 'ok', 2);
				return $data['id'];
		
    		} catch (Exception $e) {
				
				msg($e->getMessage(), 'error', 1);
				msg("Failed to update {$this->_class_name}: " . print_r($this->db->getConnection()->errorInfo(), true), 'error', 1);
				
				return false;
			}
		
		} else {
		
			msg("Onxshop_Model.update: {$this->_class_name} data are not valid", 'error');
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
        
        if (is_numeric($id)) {
        
        	try {
        	
        		$this->db->exec("DELETE FROM {$this->_class_name} WHERE id = {$id}");
        		msg("Record id:{$id} in {$this->_class_name} has been successfully deleted.", 'ok', 2);
        	
        		return true;
        		
        	} catch (Exception $e) {
					
				msg($e->getMessage(), 'error', 1);
				msg("Failed to delete {$this->_class_name}: " . print_r($this->db->getConnection()->errorInfo(), true), 'error', 1);
					
				return false;
			}

        } else {
        	msg("Missing ID", 'error');
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
			
			$this->db->exec("DELETE FROM {$this->_class_name} $where_statement");
			msg("Everything from {$this->_class_name} has been successfully deleted.", 'ok', 2);
			return true;
			
		} catch (Exception $e) {
					
			msg($e->getMessage(), 'error', 1);
			msg("Failed to deleteAll {$this->_class_name}: " . print_r($this->db->getConnection()->errorInfo(), true), 'error', 1);
					
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
			
			$records = $this->db->fetchAll($sql);
			
			if (is_array($records)) return $records;
			else return false;
			
		} catch (Exception $e) {
					
			msg($e->getMessage(), 'error', 1);
			msg("Failed to executeSql {$this->_class_name}: " . print_r($this->db->getConnection()->errorInfo(), true), 'error', 1);
					
			return false;
		}
		
	}
	
	/**
	 * query cache TODO
	 */
	 
	public function executeSqlCached($sql) {
	
		$frontendOptions = array(
		'lifetime' => ONXSHOP_DB_QUERY_CACHE_TTL,
		'automatic_serialization' => false
		);
		$backendOptions = array('cache_dir' => ONXSHOP_DB_QUERY_CACHE_DIRECTORY);
		
		$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		
		$cache_id = "{$this->_class_name}_" . md5($sql);
		
		if ($records = $cache->load($cache_id)) {
			
			$records = unserialize($records);
			
		} else {
		
			$records = $this->executeSqlOnDatabase($sql);
			$cache->save(serialize($records), $cache_id);
			
		}
		
		return $records;
		
	}
	
	/**
	 * flush cache (for this object)
	 */
	 
	public function flushCache() {
		
		$mask = ONXSHOP_DB_QUERY_CACHE_DIRECTORY . "zend_cache---{$this->_class_name}_*";
		array_map("unlink", glob( $mask ));
		$mask = ONXSHOP_DB_QUERY_CACHE_DIRECTORY . "zend_cache---internal-metadatas---{$this->_class_name}_*";
		array_map("unlink", glob( $mask ));
		
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
	 */
	 
	public function getTableInformation($table_name) {
		
		return $this->db->describeTable($table_name);
		
	}
	
}
