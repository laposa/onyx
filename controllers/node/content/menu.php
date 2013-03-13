<?php
/**
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Menu extends Onxshop_Controller_Node_Content_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * initialise node and get detail
		 */
		 
		require_once('models/common/common_node.php');
		
		$Node = new common_node();
		
		$node_data = $Node->nodeDetail($this->GET['id']);
		
		/**
		 * get node component options
		 */
		 
		if ($node_data['component']['template'] == '') $node_data['component']['template'] = 'menu_UL';
		
		if (is_numeric($node_data['component']['level'])) $level = $node_data['component']['level'];
		else $level = 0;
		if (is_numeric($node_data['component']['display_all'])) $display_all = $node_data['component']['display_all'];
		else $display_all = 0;
		if (is_numeric($node_data['component']['open'])) $open = $node_data['component']['open'];
		else $open = '';
		
		/**
		 * pass to menu component
		 */
		 
		$Onxshop_Request = new Onxshop_Request("component/menu~id={$node_data['component']['node_id']}:template={$node_data['component']['template']}:display_teaser={$node_data['component']['display_teaser']}:level={$level}:expand_all={$display_all}:open={$open}~");
		$this->tpl->assign("MENU", $Onxshop_Request->getContent());
		$this->tpl->assign("NODE", $node_data);
		
		if ($node_data['display_title'])  $this->tpl->parse('content.title');

		return true;
	}
}
