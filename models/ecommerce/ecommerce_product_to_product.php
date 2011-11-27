<?php
/**
 * class ecommerce_product_to_product
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_product_to_product extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;
	/**
	 * @access private
	 */
	var $product_id;
	/**
	 * @access private
	 */
	var $related_product_id;

	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'product_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'related_product_id'=>array('label' => '', 'validation'=>'int', 'required'=>true)
		);
		
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_product_to_product ( 
	id serial NOT NULL PRIMARY KEY,
	product_id int REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE CASCADE,
	related_product_id int REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE CASCADE 
);

ALTER TABLE ecommerce_product_to_product ADD CONSTRAINT product_id_related_product_id_key UNIQUE (product_id, related_product_id);
		";
		
		return $sql;
	}
		
	/**
	 * get related products defined by admin
	 *
	 * @param unknown_type $product_id
	 * @param unknown_type $limit
	 * @param unknown_type $type
	 * @return unknown
	 */

	function getRelatedProduct($product_id, $limit = 5, $type = 'dynamic') {
		if (is_numeric($product_id)) {
			
			switch ($type) {
				case 'static':
					$related = $this->_getRelatedProductStatic($product_id);
				break;
				case 'dynamic':
					$related = $this->_getRelatedProductDynamic($product_id, $limit);
				break;
				
			}
			
			if (is_array($related)) {

				$result = array();
				foreach ($related as $key=>$r) {
					$result[] = $r['related_product_id'];
				}
				return $result;
			} else {
				return array();
			}
		} else {
			return false;
		}
	}
	
	/**
	 * get related products defined by admin
	 *
	 * @param unknown_type $product_id
	 * @param unknown_type $published
	 * @return unknown
	 */
	
	function _getRelatedProductStatic($product_id) {
		$related = $this->listing("product_id = $product_id");
		return $related;
	}
	
	
	
	/**
	 * get related products from customers basket
	 * 
	 * @todo count all product_varieies of one product together
	 *
	 * @param unknown_type $product_id
	 * @param unknown_type $published
	 * @return unknown
	 */
	
	function _getRelatedProductDynamic($product_id, $limit = 5) {
		
		//$sql = "SELECT DISTINCT product_variety_id, count(product_variety_id) AS count FROM ecommerce_basket_content GROUP BY product_variety_id ORDER BY count $order LIMIT $limit";
		$sql = "
SELECT DISTINCT product_variety.product_id AS related_product_id, product_variety_id, count(product_variety_id) AS count 
FROM ecommerce_basket_content basket_content
LEFT OUTER JOIN ecommerce_product_variety product_variety ON (product_variety.id = product_variety_id)
LEFT OUTER JOIN ecommerce_product product ON (product.id = product_variety.product_id)
LEFT OUTER JOIN ecommerce_basket basket ON (basket.id = basket_content.basket_id)
WHERE product.publish = 1 AND basket.id IN 
(
SELECT basket_content.basket_id 
FROM ecommerce_basket_content basket_content
LEFT OUTER JOIN ecommerce_product_variety product_variety ON (basket_content.product_variety_id = product_variety.id)
WHERE product_variety.product_id = $product_id
)
AND product.id != $product_id
GROUP BY product_id, product_variety_id 
ORDER BY count DESC LIMIT $limit";
		
		if ($records = $this->executeSql($sql)) {
			return $records;
		} else {
			return false;
		}
	}
}
