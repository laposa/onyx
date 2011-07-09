<?php
/**
 * class common_taxonomy_label
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_taxonomy_label extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;
	/**
	 * @access private
	 */
	var $title;
	/**
	 * @access private
	 */
	var $description;
	/**
	 * @access private
	 */
	var $priority; //not in use, using common_taxonomy_tree.priority

	var $publish;


	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'description'=>array('label' => 'string', 'validation'=>'string', 'required'=>false),
		'priority'=>array('label' => 'int', 'validation'=>'string', 'required'=>false),
		'publish'=>array('label' => 'int', 'validation'=>'string', 'required'=>false)
		);


	/**
	 * change position (NOT IN USE IN 1.4?)
	 */
	 
	function changePosition($item_id, $position) {
		if (!is_numeric($item_id) || !is_numeric($position)) return false;
		
		//get list of all sibling
		if ($sibling_list = $this->getSiblingList($item_id)) {
			foreach ($sibling_list as $sibling) {
				msg("Sibling id {$sibling['id']} with priority {$sibling['priority']}");
			}
			
		} else {
			return false;
		}
	}
	
	/**
	 * get sibling (NOT IN USE IN 1.4?)
	 * FIXME: a database schema design problem, priority should be in taxonomy_tree
	 * or parent in taxonomy_label (parent and priority must be together)
	 */
	 
	function getSiblingList($item_id) {
		if (!is_numeric($item_id)) return false;
		
		
		if ($item_data = $this->detail($item_id)) {
			$list = $this->listing("parent = {$item_data['priority']}", 'priority ASC');
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
	
}
