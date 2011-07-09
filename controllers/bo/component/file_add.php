<?php
/** 
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/file.php');

class Onxshop_Controller_Bo_Component_File_Add extends Onxshop_Controller_Bo_Component_File {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$relation = $this->GET['relation'];
		
		$File = $this->initializeFile($relation);
		
		if ($_POST['add'] == 'add') {
		
			if ($File->insertFile($_POST['file'])) {
				msg('File inserted');
			}
		
			$this->tpl->assign('FILE', $_POST['file']);
			
		} else {
		
			$file_data['src'] = str_replace(ONXSHOP_PROJECT_DIR, "", $File->decode_file_path($this->GET['file_path_encoded']));
			$file_data['node_id'] = $this->GET['node_id'];
			$file_data['relation'] = $this->GET['relation'];
		
			if (trim($file_data['title']) == '') {
				$file_info = $File->getFileInfo(ONXSHOP_PROJECT_DIR . $file_data['src']);
				$file_data['title'] = $file_info['filename'];
				
				/**
				 * clean
				 */
				 
				$file_data['title'] = $this->cleanFileTitle($file_data['title']);
			
			}
		
			$this->tpl->assign('FILE', $file_data);
		
		}
		
		$this->tpl->assign("SELECTED_{$this->GET['role']}", "selected='selected'");

		return true;
	}
	
	/**
	 * clean title
	 */
	 
	public function cleanFileTitle($title) {
		
		$title = preg_replace('/(\.jpg)?(\.jpeg)?(\.gif)?(\.png)?(\.doc)?(\.docx)?(\.pdf)?(\.zip)?$/i', '', $title);
		$title = preg_replace('/[_-]/', ' ', $title);
		$title = ucfirst($title);
		
		return $title;
	}
}
