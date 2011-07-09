<?php

require_once('models/common/common_node_taxonomy.php');

/**
 * class ecommerce_product_variety_taxonomy
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_product_variety_taxonomy extends common_node_taxonomy {

	/**
	 * NOT NULL REFERENCES ecommerce_product_variety ON UPDATE CASCADE ON DELETE
	 * CASCADE
	 * @access private
	 */
	var $node_id;


}