<?php
/**
 * class common_node_taxonomy
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_node_taxonomy extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;
	/**
	 * NOT NULL REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	var $node_id;
	/**
	 * NOT NULL REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE RESTRICT
	 * @access private
	 */
	var $taxonomy_tree_id;


	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'taxonomy_tree_id'=>array('label' => '', 'validation'=>'int', 'required'=>true)
		);
	
	/**
	 * get label
	 */
	 
	function getLabel($taxonomy_tree_id) {
	
		require_once('models/common/common_taxonomy_tree.php');
		$TTree = new common_taxonomy_tree();
		$detail = $TTree->detailFull($taxonomy_tree_id);
		return $detail;
		
	}
	
	/**
	 * get relations
	 */
	
	function getRelationsToNode($node_id) {
	
		if (!is_numeric($node_id)) return false;
		
		$relations_list = $this->listing("node_id = $node_id");
		
		foreach($relations_list as $item) {
			$relations[] = $item['taxonomy_tree_id'];
		}
		
		return $relations;
		
	}
}
