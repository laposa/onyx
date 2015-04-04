<?php
/** 
 * Copyright (c) 2008-2015 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/file.php');

class Onxshop_Controller_Bo_Component_File_List extends Onxshop_Controller_Bo_Component_File {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$type = $this->GET['type'];
		$relation = $this->GET['relation'];
		
		$File = $this->initializeFile($relation);

		$this->tpl->assign('IMAGE_CONF', $File->conf);
		
		
		$role = $this->GET['role'];
		if (!is_numeric($this->GET['node_id'])) $this->GET['node_id'] = $_POST['file']['node_id'];
		if (is_numeric($this->GET['node_id'])) $files = $File->listFiles($this->GET['node_id'], $role);
		
		if (is_array($files)) {
			if (count($files) == 0) $this->tpl->parse('content.empty');
			else {

				foreach ($files as $file_detail) {
					
					$this->parseItem($file_detail, $type, $relation);
					
				}
				
				$this->tpl->parse("content.list");
			}
		}

		return true;
	}
	
	/**
	 * parse item
	 */
	
	public function parseItem($file_detail, $type, $relation) {
		
		$_Onxshop = new Onxshop_Request("bo/component/file_detail~file_id={$file_detail['id']}:type=$type:relation=$relation~");
					
		$this->tpl->assign('FILE_DETAIL', $_Onxshop->getContent());
										
		$this->tpl->parse("content.list.item");
	}
}
