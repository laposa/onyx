<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * not used?
 */

class Onxshop_Controller_Bo_Layout extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		/*
		$layout_mapping = unserialize(urldecode($this->GET['layout_mapping']));
		
		foreach ($layout_mapping as $id=>$lm) {
			$_nSite = new nSite("page&id=$lm");
			//$to = urlencode("xhtml.admin.bo/pages.bo/node_edit&id={$this->GET['id']}");
			//$content[$id] = $_nSite->getContent() . "<a class='onxshop_button' href='index.php?request={$this->parent_request}.bo/pages.bo/content_edit&id=$lm&page_id={$this->GET['id']}'><span>edit</span></a>";
			$content[$id] = "<div class='onxshop_editable'>" . $_nSite->getContent() . "</div>";
		}
		
		$this->tpl->assign("CONTENT", $content);
		*/

		return true;
	}
}
