<?php
/**
 * class client_group
 *
 * Copyright (c) 2011-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class client_group extends Onyx_Model {

    /**
     * primary key (serial)
     */
    public $id;
    
    /**
     * group title
     */
    public $name;
    
    /**
     * group description
     */
    public $description;
    
    /**
     * serialized filter
     */
    public $search_filter;
    
    /**
     * serialized reserved
     */
    public $other_data;
    
    /**
     * meta data 
     */
    public $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'name'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'search_filter'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
        'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false)
        );
    
    /**
     * create table sql
     * 
     * @return string
     * SQL command for table creating
     */
     
    private function getCreateTableSql() {
    
        $sql = "CREATE TABLE client_group (
            id serial NOT NULL PRIMARY KEY,
            name varchar(255) ,
            description text ,
            search_filter text ,
            other_data text
        )";
        
        return $sql;
    }
        
    /**
     * init configuration
     * 
     * @return array
     * group configuration
     */
     
    static function initConfiguration() {
    
        if (array_key_exists('client_group', $GLOBALS['onyx_conf'])) $conf = $GLOBALS['onyx_conf']['client_group'];
        else $conf = array();

        return $conf;
    }
        
    /**
     * get group detail
     * 
     * @param integer $id
     * group ID
     * 
     * @return array
     * SQL row with group informations
     */
     
    public function getDetail($id) {
    
        if (!is_numeric($id)) return false;
        
        $data = $this->detail($id);
        
        $data['search_filter'] = unserialize($data['search_filter']);
        if ($data['other_data']) $data['other_data'] = unserialize($data['other_data']);
                    
        return $data;
    }
    
    /**
     * list available groups
     * 
     * @return array
     * groups informations
     */
    
    public function listGroups() {
    
        $list = $this->listing();
        
        $final_list = array();
        
        foreach ($list as $item) {
        
            $item['search_filter'] = unserialize($item['search_filter'] ?? '');
            if ($item['other_data']) $item['other_data'] = unserialize($item['other_data']);
            
            $final_list[] = $item;
        
        }
        
        return $final_list;
    }
    
    /**
     * save group
     * 
     * @param array $data
     * group informations for save
     * 
     * @return integer
     * saved group ID or false if save failed
     */
     
    public function saveGroup($data) {
        
        if (!is_array($data)) return false;
        
        if (array_key_exists('search_filter', $data)) $data['search_filter'] = serialize($data['search_filter']);
        if (array_key_exists('other_data', $data)) $data['other_data'] = serialize($data['other_data']);
        
        return $this->save($data);
        
    }
}
