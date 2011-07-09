<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Export_Imagesxml extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		header('Content-Type: text/xml');
		
		// flash in IE with SSL dont like Cache-Control: no-cache and Pragma: no-coche
		header("Cache-Control: ");
		header("Pragma: ");
		
		require_once('models/common/common_image.php');
		$Image = new common_image();
		
		//$images = $Image->listing("role='{$this->GET['role']}' AND node_id={$this->GET['node_id']}", "priority DESC, id ASC");
		$images = $Image->listing("node_id={$this->GET['node_id']}", "priority DESC, id ASC");
		
		foreach ($images as $img) {
			$this->tpl->assign('IMAGE', $img);
			$this->tpl->parse("content.item");
		}

		return true;
	}
}
