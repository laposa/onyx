<?php
/** 
 * Copyright (c) 2012-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Social_Network_Share extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		//get input
		if (is_numeric($this->GET['node_id'])) $node_id = $this->GET['node_id'];
		else $node_id = 5; //homepage
		
		//initialise
		require_once('models/common/common_node.php');
		require_once('models/common/common_image.php');
		$Node = new common_node();
		$Image = new common_image();
		
		//get node detail
		$node_data = $Node->nodeDetail($node_id);
		if ($node_data['page_title'] == '') $node_data['page_title'] = $node_data['title'];
		
		//set URI
		$share_uri = "http://".$_SERVER['HTTP_HOST']."/page/$node_id";
		$this->tpl->assign('SHARE_URI', $share_uri);
		
		//get image detail
		$image_list = $Image->listFiles($node_id);
		if (is_array($image_list) && count($image_list) > 0) $image_detail = $image_list[0];
		else {
			$image_detail = array();
			$image_detail['src'] = 'var/files/favicon.ico';
		}
		
		//assign to template
		$this->tpl->assign('IMAGE', $image_detail);
		$this->tpl->assign('NODE', $node_data);
		
		return true;
		
	}
}
