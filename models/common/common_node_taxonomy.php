<?php
/**
 * class common_node_taxonomy
 *
 * Copyright (c) 2009-2016 Onxshop Ltd (https://onxshop.com)
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


	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'taxonomy_tree_id'=>array('label' => '', 'validation'=>'int', 'required'=>true)
		);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE common_node_taxonomy ( 
	id serial NOT NULL PRIMARY KEY,
	node_id int NOT NULL REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
	taxonomy_tree_id int NOT NULL REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE CASCADE
);
ALTER TABLE common_node_taxonomy ADD CONSTRAINT node_node_id_taxonomy_tree_id_key UNIQUE (node_id, taxonomy_tree_id);
		";
		
		return $sql;
	}
	
	/**
	 * assignNodeToTaxonomy
	 */
	 
	public function assignNodeToTaxonomy($node_id, $taxonomy_tree_id) {
		
		if (!is_numeric($node_id)) return false;
		if (!is_numeric($taxonomy_tree_id)) return false;
		
		$data = array();
		$data['node_id'] = $node_id;
		$data['taxonomy_tree_id'] = $taxonomy_tree_id;
		
		return $this->insert($data);
		
	}
	
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
		
		$relations = array();
		foreach($relations_list as $item) {
			$relations[] = $item['taxonomy_tree_id'];
		}
		
		return $relations;
		
	}
	
	/**
	 * getDetailedRelationsToNode
	 */
	
	function getDetailedRelationsToNode($node_id) {
	
		if (!is_numeric($node_id)) return false;
		
		$relations = $this->getRelationsToNode($node_id);
		
		if (is_array($relations)) {
			
			foreach($relations as $k=>$item) {
				
				$relations[$k] = $this->getLabel($item);
				
			}
		}
			
		return $relations;
		
	}
	
	/**
	 * getUsedTaxonomyLabels
	 */
	 
	public function getUsedTaxonomyLabels($order_by = 'title') {
		
		if ($order_by == 'priority') $order_by = "common_taxonomy_tree.priority DESC, taxonomy_tree_id DESC";
		else $order_by = "common_taxonomy_label.title ASC, taxonomy_tree_id DESC";
		
		$sql = "
			SELECT DISTINCT taxonomy_tree_id, common_taxonomy_label.*, common_taxonomy_tree.parent AS parent, common_taxonomy_tree.priority AS priority FROM {$this->_class_name}
				LEFT OUTER JOIN common_taxonomy_tree ON common_taxonomy_tree.id = ecommerce_recipe_taxonomy.taxonomy_tree_id
				LEFT OUTER JOIN common_taxonomy_label ON common_taxonomy_label.id = common_taxonomy_tree.label_id
			ORDER BY $order_by;
			
			";
	
		$records = $this->executeSql($sql);
		
		return $records;
	}
}
