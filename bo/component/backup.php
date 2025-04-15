<?php
/**
 * Copyright (c) 2010-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Backup extends Onyx_Controller {

    /**
     * main action
     */

    public function mainAction() {

        if (in_array($this->GET['scope'], array('database', 'project', 'both'))) $scope = $this->GET['scope'];
        else $scope = 'both';

        if (ONYX_ALLOW_BACKUP_DOWNLOAD) {

            set_time_limit(0);

            if ($filename = $this->createBackup($scope)) {

                $this->notificationEmail($filename);
                onyxGoTo("/download/var/backup/$filename");

            } else {

                msg("Can't create backup", 'error');

            }

        } else {

            msg('Sorry, this feature is disabled in your installation', 'error');

        }
    }

    /**
     * createBackup
     */

    public function createBackup($scope) {

        switch ($scope) {

            case 'database':
                $filename = $this->createDatabaseBackupFile();
            break;

            case 'project':
                $filename = $this->createProjectBackupFile();
            break;

            case 'both':
            default:
                $filename_db = $this->createDatabaseBackupFile();
                $filename = $this->createProjectBackupFile();
            break;

        }

        return $filename;
    }

    /**
     * create database backup file
     */

    public function createDatabaseBackupFile() {

        $setting = $this->getSetting();

        $filename = "{$setting['DBNAME']}.sql.gz";

        if ($this->checkPermission($setting)) {

            local_exec("backup_db {$setting['USER']} {$setting['PASSWORD']} {$setting['HOST']} {$setting['DBNAME']} {$setting['PROJECT_DIR']} $filename");

            return $filename;

        } else {

            return false;

        }

    }

    /**
     * create project backup file
     */

    public function createProjectBackupFile() {

        $setting = $this->getSetting();

        $filename = "{$_SERVER['HTTP_HOST']}.tar.gz";

        if ($this->checkPermission($setting)) {

            local_exec("backup_project {$setting['PROJECT_DIR']}  $filename");
            //local_exec("backup_onyx {$setting['ONYX_DIR']}  $setting['PROJECT_DIR'] . 'var/backups/onyx.tgz'");

            return $filename;

        } else {

            return false;

        }

    }

    /**
     * notify about created backup
     */

    private function notificationEmail($filename) {

        require_once('models/common/common_email.php');
        $EmailForm = new common_email();

        $mail_to = ONYX_SUPPORT_EMAIL;
        $mail_toname = ONYX_SUPPORT_NAME;

        $file_info = $this->getFileInfo($filename);
        $content = print_r($file_info, true);

        if ($EmailForm->sendEmail('backup_created', $content, $mail_to, $mail_toname)) {
            $this->container->set('notify', 'sent');
        } else {
            $this->container->set('notify', 'failed');
        }
    }

    /**
     * getSetting
     */

    public function getSetting() {

        $setting = array();

        $setting['USER'] = ONYX_DB_USER;
        $setting['PASSWORD'] = ONYX_DB_PASSWORD;
        $setting['HOST'] = ONYX_DB_HOST;
        //$setting['PORT'] = ONYX_DB_PORT;
        $setting['DBNAME'] = ONYX_DB_NAME;

        $setting['PROJECT_DIR'] = ONYX_PROJECT_DIR;
        $setting['ONYX_DIR'] = ONYX_DIR;

        return $setting;

    }

    /**
     * check permission
     */

    private function checkPermission($setting) {

        if (!is_readable($setting['PROJECT_DIR'])) {
            msg("backup: directory {$setting['PROJECT_DIR']} is not readable", 'error');
            return false;
        }

        if (!is_readable($setting['ONYX_DIR'])) {
            msg("backup: directory {$setting['ONYX_DIR']} is not readable", 'error');
            return false;
        }

        return true;
    }

    /**
     * getFileInfo
     */

    private function getFileInfo($filename) {

        if (!$filename) return false;
        $file_path = ONYX_PROJECT_DIR . 'var/backup/' . $filename;
        if (!file_exists($file_path) && !is_file($file_path)) return false;

        require_once('models/common/common_file.php');
        $file_info = common_file::getFileInfo($file_path);

        return $file_info;

    }
}
