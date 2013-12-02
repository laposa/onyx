<?php
/**
 * Server filesystem browser
 *
 * Copyright (c) 2006-2013 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Server_Browser_File_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * initialize
		 */
		 
		require_once('models/common/common_file.php');
		$File = new common_file();
		
		/**
		 * Setting input variables
		 * 
		 */
		
		if ($this->GET['directory']) $base_folder = $this->GET['directory'];
		else $base_folder = 'var/files/';
		if ($this->GET['open']) $open_folder = $this->GET['open'];
		else if ($_POST['open']) $open_folder = $_POST['open'];
		else if ($_SESSION['server_browser_last_open_folder'] != '') $open_folder = urlencode($_SESSION['server_browser_last_open_folder']);
		else $open_folder = "";
		$multiupload = ($this->GET['multiupload'] == 'true');
		
		/**
		 * Store opened folder to session
		 */
		
		$_SESSION['server_browser_last_open_folder'] = urldecode($open_folder);
		
		/**
		 * Setting base paths
		 * 
		 */
		
		$base_folder_full = ONXSHOP_PROJECT_DIR . $base_folder;
		$fullpath = $base_folder_full . urldecode($open_folder);
		
		$pathinfo = pathinfo($fullpath);
		
		$actual_folder = $pathinfo['dirname'] . '/';
		$actual_file = $pathinfo['basename'];
		if (is_dir($actual_folder . $actual_file)) {
			$actual_folder = $actual_folder . $actual_file . '/';
			$actual_file = '';
		}
		$relative_folder_path = str_replace($base_folder_full, '', $actual_folder);
		
		
		/**
		 * Cleaning
		 *
		 */
		//stolen from common_uri_mapping
		if (function_exists("recode_string")) {
			$new_folder = recode_string("utf-8..flat", trim($_POST['new_folder']));
		} else {
			$new_folder = iconv("UTF-8", "ASCII//IGNORE", trim($_POST['new_folder']));
		}
		
		//$new_folder = strtolower($new_folder);
		$new_folder = preg_replace("/\s/", "-", $new_folder);
		$new_folder = preg_replace("/&[^([a-zA-Z;)]/", 'and-', $new_folder);
		$new_folder = preg_replace("/[^\w-\/]/", '', $new_folder);
		$new_folder = preg_replace("/\-{2,}/", '-', $new_folder);
		
		
					
		/**
		 * Create a new folder
		 */
		
		if ($new_folder != '' && $_POST['create']) {
			$new_folder_full = $actual_folder . $new_folder;
			if (!mkdir($new_folder_full)) {
				msg("Cannot create folder $new_folder in $actual_folder", 'error');
			}
			
		}
		
		
		/**
		 * Delete file
		 */
		
		if ($this->GET['delete_file']) {
			$File->deleteFile($this->GET['delete_file']);
		}
		
		
		/**
		 * Confirm overwrite
		 */
		
		if ($_POST['overwrite'] == 'overwrite') {
			if ($File->overwriteFile($_POST['filename'], $_POST['save_dir'], $_POST['temp_file']) ) {

				if ($multiupload) $this->jsonResponse("success");

				msg('File has been overwritten');

			} else {
				msg("Can't overwrite file", 'error');
			}
		}
		
		//input
		$normal_formated_files = $_FILES;
		
		foreach ($normal_formated_files as $file_item) {
		
			if (is_uploaded_file($file_item['tmp_name'])) {
				
				$save_dir = $base_folder . $relative_folder_path;
				$upload = $File->getSingleUpload($file_item, $save_dir);

				/**
				 * when array is returned by getSingleUpload, it's an existing file is in place
				 */

				if (is_array($upload)) {
				
					$this->tpl->assign("OVERWRITE_FILE", $upload);

					if ($multiupload) $this->jsonResponse("file_exists", $upload);

					$this->tpl->parse("content.confirm_overwrite");
					$overwrite_show = 1;
					
				} else if ($upload) {

					if ($multiupload) $this->jsonResponse("success");

					msg("Uploaded {$file_item['name']}");
				}
			}
		}
		
		/**
		 * prepare folder head string
		 */
		
		$folder_head = str_replace('/', ' / ', $relative_folder_path);
		$folder_head = '/ ' . $folder_head;
		
		/**
		 * Assign template variables
		 * 
		 */
		
		$this->tpl->assign('BASE', $base_folder);
		$this->tpl->assign('FOLDER_HEAD', $folder_head);
		$this->tpl->assign('FOLDER', $relative_folder_path);
		$this->tpl->assign('MAX_FILE_SIZE', round($this->convertBytes(ini_get('upload_max_filesize')) / 1048576));
		$this->tpl->assign('MAX_FILES', ini_get('max_file_uploads'));
		
		/**
		 * allow to upload only in non-root folder
		 */
		
		if ($relative_folder_path || ONXSHOP_MEDIA_LIBRARY_ROOT_UPLOAD) {
			
			$this->tpl->parse('content.add_new.upload_file');
			
		} else {
			
			$this->tpl->parse('content.add_new.upload_instruction');
			
		}
		
		//hide upload when overwrite?
		if ($overwrite_show == 0) {
			$this->tpl->parse("content.add_new");
		}
		
		/**
		 * Get File List
		 * 
		 */
		
		//list content od folder
		$list = $File->getFlatArrayFromFs($actual_folder, '-type f -maxdepth 1');
		//FIND2GLOB PATCH: $list = $File->getFlatArrayFromFs($actual_folder, 'f', false);
		
		if (is_array($list) && count($list) > 0) {
			foreach ($list as $l) {
				$l['file_path'] = $actual_folder . $l['name'];
				$l['file_path_encoded'] = $File->encode_file_path($actual_folder . $l['name']);
				$l['file_path_encoded_relative'] = $File->encode_file_path($base_folder . $relative_folder_path . $l['name']);
				
				$this->tpl->assign("ITEM", $l);
		
				$relations_list = $File->getRelations($base_folder.$relative_folder_path.$l['name']);
		
				if ($relations_list['count'] == 0) {
					$this->tpl->parse('content.list.item.delete');
				} else {
					$this->tpl->assign("FILE_USAGE", $relations_list['count']);
					$this->tpl->parse('content.list.item.usage');
					//$_Onxshop_Request = new Onxshop_Request("bo/component/file_usage~file_path_encoded_relative={$l['file_path_encoded_relative']}~");
					//$this->tpl->assign("FILE_USAGE", $_Onxshop_Request->getContent());
				}
		
				//if (preg_match('/^image\/.*/', $l['mime-type'])) 
				$this->tpl->parse('content.list.item.thumbnail');
				switch ($this->GET['type']) {
					case 'add_to_node':
					case 'RTE':
					case 'add_to_product':
					case 'add_to_product_variety':
					case 'add_to_recipe':
					case 'add_to_store':
					case 'add_to_survey':
					case 'add_to_taxonomy':
					case 'CSS':
					case 'file':
						$this->tpl->parse('content.list.item.add_to_node');
					break;
					case 'database_import':
						$this->tpl->parse('content.list.item.database_import');
					break;
				}
				$this->tpl->parse('content.list.item');
			}
			
			$this->tpl->parse('content.list');
			
		} else {
			// contains no files, check if there aren't some folders, otherwise allow delete
			$list = $File->getFlatArrayFromFs($actual_folder, '-type d -maxdepth 1');
			//FIND2GLOB PATCH: $list = $File->getFlatArrayFromFs($actual_folder, 'd', false);
			if (count($list) == 0) $this->tpl->parse('content.empty');
		}

		return true;
	}

	/**
	 * Convert a shorthand byte value from a PHP configuration directive to an integer value
	 * @param    string   $value
	 * @return   int
	 */
	protected function convertBytes($value)
	{
	    if (is_numeric($value)) {
	        return $value;
	    } else {
	        $value_length = strlen($value);
	        $qty = substr($value, 0, $value_length - 1);
	        $unit = strtolower(substr($value, $value_length - 1));
	        switch ($unit) {
	            case 'k':
	                $qty *= 1024;
	                break;
	            case 'm':
	                $qty *= 1048576;
	                break;
	            case 'g':
	                $qty *= 1073741824;
	                break;
	        }
	        return $qty;
	    }
	}

	/**
	 * Display JSON response and cancel script execution
	 * 
	 * @param  String $status Status attribute value 
	 * @param  array  $data   Addional response data
	 */
	protected function jsonResponse($status, $data = array())
	{
		$data['status'] = $status;
		echo json_encode($data);
		exit();
	}
}	
		
