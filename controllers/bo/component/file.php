<?php
/** 
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_File extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$file_id = $this->GET['file_id'];
		$type = $this->GET['type'];
		$relation = $this->GET['relation'];
		
		$File = $this->initializeFile($relation);

		$this->tpl->assign('IMAGE_CONF', $File->conf);


		return true;
	}
	
	/**
	 * initialize file
	 */
	 
	public function initializeFile($relation) {
	
		switch ($relation) {
			case 'product':
				require_once('models/ecommerce/ecommerce_product_image.php');
				$File = new ecommerce_product_image();
			break;
			case 'product_variety':
				require_once('models/ecommerce/ecommerce_product_variety_image.php');
				$File = new ecommerce_product_variety_image();
			break;
			case 'taxonomy':
				require_once('models/common/common_taxonomy_label_image.php');
				$File = new common_taxonomy_label_image();
			break;
			case 'node':
				require_once('models/common/common_image.php');
				$File = new common_image();
			break;
			case 'print_article':
				require_once('models/common/common_print_article.php');
				$File = new common_print_article();
			break;
			case 'file':
			default:
				require_once('models/common/common_file.php');
				$File = new common_file();
			break;
		}
		
		return $File;
	}
}
