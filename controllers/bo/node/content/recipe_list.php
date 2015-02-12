<?php
/**
 * Copyright (c) 2013-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Recipe_List extends Onxshop_Controller_Bo_Node_Content_Default {

	/**
	 * pre action
	 */

	function pre() {
	
		parent::pre();
		
		if (!(is_numeric($_POST['node']['component']['limit']) && $_POST['node']['component']['limit'] > 0)) $_POST['node']['component']['limit'] = 10;
		if ($_POST['node']['component']['pagination'] == 'on') $_POST['node']['component']['pagination'] = 1;
		else $_POST['node']['component']['pagination'] = 0;

	}

	/**
	 * post action
	 */
	 
	function post() {
		
		parent::post();
		
		//template
		$this->tpl->assign("SELECTED_template_{$this->node_data['component']['template']}", "selected='selected'");
		
		//sort-by
		$this->tpl->assign("SELECTED_sort_by_{$this->node_data['component']['sort']['by']}", "selected='selected'");
		
		//sort-order
		$this->tpl->assign("SELECTED_sort_direction_{$this->node_data['component']['sort']['direction']}", "selected='selected'");

		//pagination
		$this->node_data['component']['pagination']        = ($this->node_data['component']['pagination']) ? 'checked="checked"'      : '';
		
	}
}
