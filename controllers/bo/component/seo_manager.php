<?php
/**
 * SEO manager
 *
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


class Onxshop_Controller_Bo_Component_Seo_Manager extends Onxshop_Controller {	
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		require_once('models/common/common_node.php');
		$Node = new common_node();

		require_once('models/common/common_uri_mapping.php');
		$Mapping = new common_uri_mapping();
		
		$uri_list = $Mapping->getDetailList();
		//print_r($uri_list);
		foreach ($uri_list as $item) {
			
			if ($item['type'] == '301') {
				$item['title'] = '';
				$item['teaser'] = '';
				$item['description'] = '';
				$item['keywords'] = '';
			}
			
			$this->tpl->assign('ITEM', $item);
			
			$this->tpl->parse('content.item');
		}
            
		return true;
		
	}
	
}
