<?php
/**
 * class common_taxonomy_tree
 *
 * Copyright (c) 2009-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_taxonomy_tree extends Onxshop_Model {

    /**
     * @access private
     */
    var $id;
    /**
     * NOT NULL REFERENCES common_taxonomy_label ON UPDATE CASCADE ON DELETE RESTRICT
     * @access private
     */
    var $label_id;
    /**
     * NOT NULL REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE CASCADE
     * @access private
     */
    var $parent;

    var $priority;

    var $publish; //not in use yet, use taxonomy.publish instead

    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'label_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'parent'=>array('label' => 'Parent', 'validation'=>'int', 'required'=>false),
        'priority'=>array('label' => 'Priority', 'validation'=>'int', 'required'=>false),
        'publish'=>array('label' => 'Publish', 'validation'=>'int', 'required'=>false)
        );

    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE common_taxonomy_tree ( 
  id serial PRIMARY KEY NOT NULL,
  label_id int NOT NULL REFERENCES common_taxonomy_label ON UPDATE CASCADE ON DELETE CASCADE,
  parent int  REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE CASCADE,
  priority smallint DEFAULT 0 NOT NULL,
  publish smallint DEFAULT 1 NOT NULL

);
        ";
        
        return $sql;
    }
    
    /**
     * get the tree
     *
     * @param unknown_type $publish
     * @return unknown
     */
     
    function getTree($publish = 0) {
        $add_to_where = '';
        
        if (is_numeric($publish)) {
            if ($publish == 1) $add_to_where = "AND label.publish = 1";
        }
        
        $sql = "SELECT tree.id, tree.parent AS parent, label.title AS name, label.title AS title, label.description, tree.priority, label.publish FROM common_taxonomy_tree tree, common_taxonomy_label label WHERE tree.label_id = label.id $add_to_where ORDER BY tree.priority DESC, label.title ASC";

        $records = $this->executeSql($sql);
        
        return $records;
    }

    /**
     * get full path detail
     */
     
    function getFullPathDetail($id) {
        msg("Calling Taxonomy:getFullPath($id)", 'error', 2);
        
        $path = array();
        $i = 0;
        
        if ($id > 0) {
            $path[$i] = $this->detailFull($id);
            $parent = $path[$i]['parent'];
        
            while($parent > 0) {
                $i++;
                $path[$i] = $this->detailFull($path[$i-1]['parent']);
                
                $parent = $path[$i]['parent'];
            }
        
        }
        return $path;
    }
    
    /**
     * full detail
     */

    function detailFull($id) {
    
        $detail = $this->detail($id);
        
        require_once('models/common/common_taxonomy_label.php');
        $Label = new common_taxonomy_label();
        $label_data = $Label->detail($detail['label_id']);
        $detail['label'] = $label_data;
        
        return $detail;
    }
    
    /**
     * get fullpath detail for breadcrumb
     */
     
    function getFullPathDetailForBreadcrumb($id) {
    
        $path = $this->getFullPathDetail($id);
        $path = array_reverse($path);
        
        return $path;
    }
    
    /**
     * move item
     */
    
    function moveItem($source_id, $destination_id, $position) {
    
        if (!is_numeric($source_id) || !is_numeric($destination_id) || !is_numeric($position)) return false;
        
        if ($destination_id == 0) $destination_id = null; //root element is NULL
        
        //change parent
        if (!$this->updateSingleAttribute('parent', $destination_id, $source_id)) return false;
        
        //changePosition 
        if ($this->changePosition($source_id, $position)) return true;
        else return false;

        return true;
    }

    /**
     * change position
     */

    function changePosition($item_id, $position) {
        //msg("item_id: $item_id, position: $position");
        
        if (!is_numeric($item_id) || !is_numeric($position)) return false;

        //get list of all sibling
        if ($sibling_list = $this->getSiblingList($item_id)) {
            $position_reverse = count($sibling_list) - $position;

            $i = 1;
            foreach ($sibling_list as $sibling) {
                //msg("$key Sibling id {$sibling['id']} with priority {$sibling['priority']}");
                if ($sibling['id'] == $item_id) {
                    $this->updateSingleAttribute('priority', $position_reverse*10 - 5, $sibling['id']);
                } else {
                    $this->updateSingleAttribute('priority', $i*10, $sibling['id']);
                    $i++;
                }
            }

            return true;
        } else {
            msg("common_taxonomy_tree.changePosition(): cannot get sibling list for item id $item_id", 'error');
            return false;
        }
    }

    /**
     * get sibling
     */

    function getSiblingList($item_id) {
    
        if (!is_numeric($item_id)) return false;

        if ($item_data = $this->detail($item_id)) {
            
            $order = 'priority ASC, id DESC';
            
            if (is_null($item_data['parent'])) $list = $this->listing("parent IS NULL", $order);
            else $list = $this->listing("parent = {$item_data['parent']}", $order);
        
        } else {
            return false;
        }
        
        if (is_array($list)) return $list;
        else return false;
    }
    
    
    /**
     * temporary implementation (will be in general model in future)
     */
    
    function updateSingleAttribute($attribute, $update_value, $id) {
    
        switch ($attribute) {
            case 'parent':
                //safety check
                if ($id == $update_value) {
                    msg("common_taxonomy_tree: parent cannot be identical to id", 'error');
                    return false;
                }
                
                $data = $this->detail($id);
                
                if (is_array($data)) {
                    $data['parent'] = $update_value;
                    
                    if ($this->update($data)) return true;
                    else return false;
                }
            break;
            case 'priority':
                $data = $this->detail($id);
                if (is_array($data)) {
                    $data['priority'] = $update_value;
                    if ($this->update($data)) return true;
                    else return false;
                }
            break;
        }
    }
    
    /**
     * getRelatedTaxonomy
     */
     
    public function getRelatedTaxonomy($node_id, $relation = 'common_node_taxonomy') {
    
        if (!is_numeric($node_id)) return false;
        if (!in_array($relation, array('common_node_taxonomy', 'ecommerce_product_taxonomy', 
            'ecommerce_product_variety_taxonomy', 'ecommerce_recipe_taxonomy',
            'ecommerce_store_taxonomy', 'client_customer_taxonomy'))) return false;
        
        $sql = "
            SELECT tree.id, tree.parent, tree.priority, label.publish, label.title, label.description  FROM $relation t
LEFT OUTER JOIN common_taxonomy_tree tree ON (t.taxonomy_tree_id = tree.id)
LEFT OUTER JOIN common_taxonomy_label label ON (tree.label_id = label.id)
WHERE node_id = $node_id ORDER BY tree.priority DESC, tree.id ASC
        ";
    
        $records = $this->executeSql($sql);
        
        return $records;
        
    }
}
