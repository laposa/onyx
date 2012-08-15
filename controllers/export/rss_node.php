<?php
/** 
 * Copyright (c) 2007-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Export_Rss_Node extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * initialize
		 */
		 
		require_once('models/common/common_node.php');
		require_once('models/common/common_image.php');
		$Node = new common_node();
		$Image = new common_image();
		
		/**
		 * find node id
		 */
		 
		if (is_numeric($this->GET['id'])) {
			$id = $this->GET['id'];
		} else {
			$id = $Node->conf['id_map-blog'];
		}

		/**
		 * set header 
		 */
		 
		header('Content-Type: text/xml; charset=UTF-8');
		// flash in IE with SSL dont like Cache-Control: no-cache and Pragma: no-coche
		header("Cache-Control: ");
		header("Pragma: ");
		
		/**
		 * latest date
		 */
		 
		$rss_date = date('D, d M Y g:i:s O', time());
		$this->tpl->assign("RSS_DATE", $rss_date);
		
		/**
		 * check
		 */
		 
		if (!is_numeric($id)) {
			msg('export rss: id is not numeric', 'error');
			return false;
		}
		
		/**
		 * process
		 */
		 
		$node_data = $Node->getDetail($id);
		
		if ($node_data['publish'] == 1) {
		
			$this->tpl->assign('NODE', $node_data);
		
			$children = $Node->listing("parent = $id AND publish = 1 AND node_group='page'", "created DESC");
			
			foreach ($children as $c) {
				
				/**
				 * create public link
				 */
				 
				$link = $Node->getSeoURL($c['id']);
				$c['url'] = "http://{$_SERVER['HTTP_HOST']}{$link}";
				
				/**
				 * format date
				 */
				 
				$c['rss_date'] = date('D, d M Y G:i:s O', strtotime($c['created']));
				
				/**
				 * add image (not part of RSS spec)
				 */
				
				$teaser_image = $Image->getTeaserImageForNodeId($c['id']);
				
				if ($teaser_image) $c['image'] = "http://{$_SERVER['HTTP_HOST']}/image/{$teaser_image['src']}";
				else $c['image'] = '';
				
				/**
				 * assign
				 */
				 
				$this->tpl->assign('CHILD', $c);
				$this->tpl->parse("content.item");
			
			}
		}

		return true;
	}
}
