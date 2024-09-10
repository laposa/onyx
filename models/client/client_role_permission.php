<?php
/**
 *
 * Copyright (c) 2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class client_role_permission extends Onyx_Model {

    /**
     * Permission cache to prevent multiple request in single script run
     */
    private static $permissionCache = array();

    /**
     * @private
     */
    var $id;

    /**
     * @private
     */
    var $role_id;
    
    /**
     * @private
     */
    var $resource;
    
    /**
     * @private
     */
    var $operation;

    /**
     * @private
     */
    var $scope;

    /**
     * @private
     */
    var $created;

    /**
     * @private
     */
    var $modified;

    /**
     * @private
     */
    var $other_data;

    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'role_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'resource'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'operation'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'scope'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
        'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
        'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
    );

    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
        CREATE TYPE acl_operation AS ENUM (
            '_all_',
            'view',
            'edit',
            'add',
            'delete',
            'publish'
        );
        CREATE TYPE acl_resource AS ENUM (
            '_all_',
            'front_office',
            'back_office',
            'nodes',
            'products',
            'recipes',
            'stores',
            'orders',
            'stock',
            'customers',
            'reports',
            'discounts',
            'comments',
            'surveys',
            'media',
            'taxonomy',
            'seo_manager',
            'database',
            'templates',
            'scheduler',
            'currency',
            'search_index',
            'tools',
            'logs',
            'configuration',
            'permissions',
            'build'
        );
        CREATE TABLE client_role_permission (
            id serial NOT NULL PRIMARY KEY,
            role_id integer NOT NULL REFERENCES client_role ON UPDATE CASCADE ON DELETE CASCADE,
            resource acl_resource,
            operation acl_operation,
            scope text,
            created timestamp without time zone NOT NULL DEFAULT NOW(),
            modified timestamp without time zone NOT NULL DEFAULT NOW(),
            other_data text
        );
        CREATE INDEX client_role_permission_role_id_key ON client_role_permission USING btree (role_id);
        ";
            
        return $sql;
    }
    
    /**
     * Check if given customer can access back office
     */
     
    public function isBackofficeUser($customer_id)
    {
        if (!is_numeric($customer_id)) return false;
        
        $list = $this->listing("role_id IN (SELECT role_id FROM client_customer_role WHERE customer_id = $customer_id)");
        
        if (count($list) > 0) return true;
        else return false;
    }

    /**
     * Return true if given customer has given permission
     * @param  int       $customer_id Customer Id
     * @param  string    $resource    Resource
     * @param  string    $operation   Operation (wildcard operation "_any_" is accepted)
     * @param  int|int[] $scope       Limit to resource id or ids (array), permission can be configured with id_whitelist or id_blacklist array
     * @return bool
     */
    public function checkPermissionByCustomer($customer_id, $resource, $operation, $scope = null)
    {
        if (!is_numeric($customer_id)) return false;

        // store customer's permission to static variable (kind of cache invalidated by the end of script )
        if (!isset(self::$permissionCache[$customer_id])) {
            self::$permissionCache[$customer_id] = $this->getAllCustomersPermissions($customer_id);
        }

        foreach (self::$permissionCache[$customer_id] as $item) {
            
            if ($item['resource'] == "_all_" && $this->checkScope($item, $scope)) return true; 
            
            if ($item['resource'] == $resource) {
                
                if (($item['operation'] == "_all_" || 
                    $item['operation'] == $operation ||
                    $operation == "_any_") && $this->checkScope($item, $scope)) return true;

            }
        }

        return false;
    }

    /**
     * Check if given permission is allowed for the scope
     * @param client_role_permission $item 
     * @param int|int[] $scope
     */
    protected function checkScope($item, $scope = null) 
    {
        // if no scope is specified, permission is allowed
        if (!$scope || !$item['scope']) return true;

        // id_whitelist and/or id_blacklist can be configured
        $permissionScope = unserialize($item['scope']);
        if (!$permissionScope['id_whitelist'] && !$permissionScope['id_blacklist']) return true;

        if (!is_array($scope)) $scope = [$scope];

        // check if scope is in whitelist
        if ($permissionScope['id_whitelist']) {
            $whiteListed = false;
            foreach ($scope as $id) {
                if (in_array($id, $permissionScope['id_whitelist'])) $whiteListed = true;
            }

            if (!$whiteListed) return false;
        }

        // check if scope is in blacklist
        if ($permissionScope['id_blacklist']) {
            foreach ($scope as $id) {
                if (in_array($id, $permissionScope['id_blacklist'])) return false;
            }
        }

        return true;
    }

    /**
     * Get all customer's permission as array where its index
     * is permission
     * @param  [type] $customer_id [description]
     * @return [type]              [description]
     */
    public function getAllCustomersPermissions($customer_id)
    {
        if (!is_numeric($customer_id)) return false;

        $sql = "SELECT * FROM client_role_permission WHERE role_id IN (SELECT role_id FROM client_customer_role WHERE customer_id = $customer_id)";
        return $this->executeSql($sql);

    }
}
