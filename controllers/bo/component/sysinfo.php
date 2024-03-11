<?php
/** 
 * Copyright (c) 2011-2012 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Component_Sysinfo extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * get user info
         */
         
        $user_details = $_SESSION['authentication']['user_details'];
        $this->tpl->assign('USER_DETAILS', $user_details);
        
        /**
         * get basic system info
         * available only to debug hosts
         */
        
        /*
        if (!ONYX_IS_DEBUG_HOST) {
            msg('Need to be listed as $debug_hosts in conf/deployment.php', 'error');
            return false;
        }
        */
        
        $sysinfo = $this->getSysInfo();
        $this->tpl->assign('SYSINFO', $sysinfo);
            
        return true;
        
    }
    
    /**
     * getSysInfo
     */
     
    public function getSysInfo() {

        $sysinfo = array();
            
        $sysinfo['uname'] = php_uname();
        $sysinfo['uptime'] = shell_exec('uptime');
        $sysinfo['id'] = shell_exec('id');
        $sysinfo['pwd'] = getcwd();
        $sysinfo['server_software'] = getenv('SERVER_SOFTWARE');
        $sysinfo['php'] = phpversion();
        $sysinfo['name'] = $_SERVER['SERVER_NAME'];
        $sysinfo['ip_local'] = gethostbyname($_SERVER['SERVER_ADDR']);
        $sysinfo['ip_public'] = gethostbyname($sysinfo['name']);
        $sysinfo['free_bits'] = diskfreespace($sysinfo['pwd']);
        $sysinfo['free'] = $this->resize_bytes($sysinfo['free_bits']);
        $sysinfo['all_bits'] = disk_total_space($sysinfo['pwd']);
        $sysinfo['all'] = $this->resize_bytes($sysinfo['all_bits']);
        $sysinfo['used'] = $this->resize_bytes($sysinfo['all_bits'] - $sysinfo['free_bits']);
        $sysinfo['os'] = PHP_OS;
        $sysinfo['db_host'] = getenv('ONYX_DB_HOST');
        $sysinfo['db_name'] = getenv('ONYX_DB_NAME');
    
        return $sysinfo;

    }
    
    /**
     * byte format
     * copied from models/common/common_file.php
     *
     * @param unknown_type $size
     * @return unknown
     */
     
    function resize_bytes($size) {
    
       $count = 0;
       $format = array("B","KB","MB","GB","TB","PB","EB","ZB","YB");
       while(($size/1024)>1 && $count<8)
       {
           $size=$size/1024;
           $count++;
       }
       $return = number_format($size,0,'','.')." ".$format[$count];
       return $return;
    }
}
