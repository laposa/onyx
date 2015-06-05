<?php
/** 
 * Google Sitemap
 *
 * Copyright (c) 2008-2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Export_Xml_Googlesitemap extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		set_time_limit(0);
		
		require_once('models/common/common_node.php');
		
		$Node = new common_node();
		
		$sitemap = $Node->getFlatSitemap();
		
		if (is_array($sitemap)) {
		
			if ($_SERVER['SSL_PROTOCOL'] || $_SERVER['HTTPS']) $protocol = 'https';
			else $protocol = 'http';

			foreach ($sitemap as $node) {
		
				$link = $Node->getSeoURL($node['id']);
				
				$item['loc'] = "$protocol://{$_SERVER['HTTP_HOST']}{$link}";
				$item['lastmod'] = $Node->getLastMod($node['id'], $node['modified']);
				$item['lastmod'] = substr($item['lastmod'], 0, 10);
				if ($node['parent'] == $Node->conf['id_map-global_navigation'] || $node['parent'] == $Node->conf['id_map-primary_navigation'] || $node['parent'] == $Node->conf['id_map-footer_navigation']) {
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
