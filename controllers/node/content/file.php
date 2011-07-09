<?php
/**
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_File extends Onxshop_Controller_Node_Content_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/common/common_node.php');	
		$Node = new common_node();
		
		$node_data = $Node->nodeDetail($this->GET['id']);
		
		require_once('models/common/common_file.php');
		$File = new common_file();
		
		if (is_numeric($this->GET['id'])) $files = $File->listFiles($this->GET['id']);
		
		if (is_array($files)){
			foreach ($files as $file) {
				$this->tpl->assign('FILE', $file);
				$this->tpl->parse('content.item');
			}
		}
		
		$this->tpl->assign('NODE', $node_data);
		
		/**
		 * display title
		 */
		 
		if ($node_data['display_title'])  {
			if ($node_data['link_to_node_id'] > 0) $this->tpl->parse('content.title_link');
			else $this->tpl->parse('content.title');
		}
		
		return true;
	}
}
