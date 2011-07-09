<?php
require_once('models/common/common_image.php');

/**
 * class ecommerce_product_image
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_product_image extends common_image {

	/**
	 * init configuration
	 */
	/*
	static function initConfiguration() {
	
		$image_default_conf = common_image::initImageDefaultConfiguration();
		
		if (array_key_exists('ecommerce_product_image', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_product_image'];
		else $conf = array();

		if (is_array($conf)) $conf = array_merge($image_default_conf, $conf);
		else $conf = $image_default_conf;

		if ($conf['cycle_fx'] == '') $conf['cycle_fx'] = $image_default_conf['cycle_fx'];
		if ($conf['cycle_easing'] == '') $conf['cycle_easing'] = $image_default_conf['cycle_easing'];
		if (!is_numeric($conf['cycle_timeout'])) $conf['cycle_timeout'] = $image_default_conf['cycle_timeout'];
		if (!is_numeric($conf['cycle_speed'])) $conf['cycle_speed'] = $image_default_conf['cycle_speed'];
		
		return $conf;
	}*/
}
