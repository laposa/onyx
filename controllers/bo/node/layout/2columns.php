<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/node/default.php');

class Onxshop_Controller_Bo_Node_Layout_2columns extends Onxshop_Controller_Bo_Node_Default {

	/**
	 * pre action
	 */
	 	
	function pre() {
		if ($_POST['component']['display_title'] == 'on') $_POST['component']['display_title'] = 1;
		else $_POST['component']['display_title'] = 0;
	}
	
	/**
	 * post action
	 */
	 
	function post() {	
		
		//layout styles
		$this->renderLayoutStyles();

	}
	
	/**
	 * layout styles
	 */
	 
	private function renderLayoutStyles() {
	
		$styles = $this->getLayoutStyles();
		
		foreach ($styles as $k=>$style) {
			$style['key'] = $k;
			$this->tpl->assign("STYLE", $style);
			if ($this->node_data['layout_style'] == $style['key']) $this->tpl->assign("SELECTED", "selected='selected'");
			else $this->tpl->assign("SELECTED", "");
			$this->tpl->parse("content.style.item");
		}
		
		$this->tpl->parse("content.style");
	}
	
	/**
	 * get styles
	 */
	 
	public function getLayoutStyles() {
		require_once('conf/node_layout_style.php');
		
		return $layout_style['layout']['styles'];
	}
}
