<?php
/** 
 * Google Sitemap
 *
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Export_Xml_Googlesitemap extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		require_once('models/common/common_node.php');
		
		$Node = new common_node();
		
		$sitemap = $Node->getFlatSitemap();
		
		if (is_array($sitemap)) {
		
			foreach ($sitemap as $node) {
		
				$link = $Node->getSeoURL($node['id']);
				
				$item['loc'] = "http://{$_SERVER['HTTP_HOST']}{$link}";
				$item['lastmod'] = $Node->getLastMod($node['id'], $node['modified']);
				$item['lastmod'] = substr($item['lastmod'], 0, 10);
				if ($node['parent'] == $Node->conf['id_map-globalmenu'] || $node['parent'] == $Node->conf['id_map-mainmenu'] || $node['parent'] == $Node->conf['id_map-footermenu']) {
					$item['priority'] = 1;
				} else {
					$item['priority'] = 0.5;
				}
				$this->tpl->assign("ITEM", $item);
				$this->tpl->parse("content.item");
			}
		}
		
		header('Content-Type: text/xml; charset=UTF-8');

		return true;
	}
}
