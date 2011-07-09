<?php
/**
 * class ecommerce_basket_content
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_basket_content extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;
	/**
	 * REFERENCES basket(id) ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	var $basket_id;
	/**
	 * REFERENCES product_variety(id) ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	var $product_variety_id;
	/**
	 * @access private
	 */
	var $quantity;
	
	var $price_id;
	
	var $other_data;

	/*REFERENCES ecommerce_product_type ON UPDATE CASCADE ON DELETE RESTRICT*/
	var $product_type_id;
	
	var $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'basket_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'product_variety_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'quantity'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'price_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'other_data'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'product_type_id'=>array('label' => '', 'validation'=>'int', 'required'=>true)
	);
	
	/**
	 * insert data
	 */
	 
	function insertItem($data) {
		$data['other_data'] = serialize($data['other_data']);
		
		if ($id = $this->insert($data)) {
			msg("Item has been added to basket.", 'ok', 2);
			return $id;
		} else {
			return false;
		}

	}
	
	/**
	 * get items
	 */

	function getItems($basket_id) {
		if (!is_numeric($basket_id)) return false;
		
		$basket_content_data = array();
		
		$basket_content_data = $this->listing("basket_id={$basket_id}");
		
		return $basket_content_data;
	}

}
