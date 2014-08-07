<?php
/**
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */
require_once('models/common/common_configuration.php');
require_once('models/common/common_node.php');

class Onxshop_Controller_Bo_Component_News_Section_Select extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		$blog_node_id = $this->GET['blog_node_id'];

		$Conf = new common_configuration();
		$conf = $Conf->listing("object = 'common_node' AND property = 'id_map-blog'");

		if (!$conf || count($conf) == 0) return true;

		$Node = new common_node();
		$node_conf = $Node::initConfiguration();
		$default_blog_node_id = (int) $node_conf['id_map-blog'];

		$list = array($default_blog_node_id => $default_blog_node_id);

		foreach ($conf as $item) {
			$id = (int) $item['value'];
			$list[$id] = $id;
		}

		if (count($list) > 0) {

			foreach ($list as $id) {
				$node = $Node->getDetail($id);
				$this->tpl->assign('NODE', $node);
				$this->tpl->assign('SELECTED', $id == $blog_node_id ? 'selected="selected"' : '');
				$this->tpl->parse('content.select.item');
			}

			$this->tpl->parse('content.select');

		}

		return true;
	}

}
		
