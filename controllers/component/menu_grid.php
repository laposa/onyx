<?php
/** 
 * Copyright (c) 2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/menu.php');

class Onxshop_Controller_Component_Menu_Grid extends Onxshop_Controller_Component_Menu {

	public function parseItem($item)
	{
		$_Onxshop_Request = new Onxshop_Request("component/teaser~target_node_id={$item['id']}~");
		$item['teaser_content'] = $_Onxshop_Request->getContent();

		return parent::parseItem($item);
	}

}
