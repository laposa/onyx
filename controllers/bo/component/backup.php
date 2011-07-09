<?php
/**
 * Copyright (c) 2010-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Backup extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		$package = ONXSHOP_PACKAGE_NAME;
		//TODO: remove gold and silver when transition finished
		if ($package == 'gold' || $package == 'silver' || $package == 'premium') {
			//$date = date("Y-m-d");
			$filename = "{$_SERVER['HTTP_HOST']}.tar.gz";
			if ($this->createBackupFile($filename)) onxshopGoTo("/download/var/backup/$filename");
			else msg("Can't create backup", 'error');
		} else {
			msg('Sorry, this feature is available only in Silver or Gold package', 'error');
		}
	}
	
	/**
	 * create backup file
	 */
	
	public function createBackupFile($filename) {
	
		set_time_limit(0);
		
		$setting['USER'] = ONXSHOP_DB_USER;
		$setting['PASSWORD'] = ONXSHOP_DB_PASSWORD;
		$setting['HOST'] = ONXSHOP_DB_HOST;
		//$setting['PORT'] = ONXSHOP_DB_PORT;
		$setting['DBNAME'] = ONXSHOP_DB_NAME;
		
		$setting['PROJECT_DIR'] = ONXSHOP_PROJECT_DIR;
		$setting['ONXSHOP_DIR'] = ONXSHOP_DIR;
		
		if ($this->checkPermission($setting)) {
		
			$this->notifyEmail();
		
			local_exec("backup {$setting['USER']} {$setting['PASSWORD']} {$setting['HOST']} {$setting['DBNAME']} {$setting['PROJECT_DIR']} {$setting['ONXSHOP_DIR']} $filename");
		
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * notify about created backup
	 */
	 
	private function notifyEmail() {
	
		require_once('models/common/common_email_form.php');
	    $EmailForm = new common_email_form();
	    
	    $mail_to = ONXSHOP_SUPPORT_EMAIL;
	    $mail_toname = ONXSHOP_SUPPORT_NAME;
	    
	    $content = array();
	    
	    if ($EmailForm->sendEmail('backup_created', $content, $mail_to, $mail_toname, $EmailForm->conf['mail_recipient_address'], $EmailForm->conf['mail_recipient_name'])) {
	    	Zend_Registry::set('notify', 'sent');
	    } else {
	    	Zend_Registry::set('notify', 'failed');
	    }
	}
	
	/**
	 * check permission
	 */
	 
	private function checkPermission($setting) {
	
		if (!is_readable($setting['PROJECT_DIR'])) {
			msg("backup: directory {$setting['PROJECT_DIR']} is not readable", 'error');
			return false;
		}
		
		if (!is_readable($setting['ONXSHOP_DIR'])) {
			msg("backup: directory {$setting['ONXSHOP_DIR']} is not readable", 'error');
			return false;
		}
		
		return true;
	}
}
