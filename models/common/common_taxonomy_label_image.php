<?php
require_once('models/common/common_image.php');

/**
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * class common_taxonomy_label_image
 * NOT NULL REFERENCES ecommerce_taxonomy_label(id) ON UPDATE CASCADE ON DELETE
 * CASCADE
 * Norbert Laposa @ Laposa Ltd, 2010/01/13
 *
 */
 
class common_taxonomy_label_image extends common_image {

	/**
	 * @access private
	 */
	var $node_id;


}
