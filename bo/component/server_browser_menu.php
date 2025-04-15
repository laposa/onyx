<?php
/**
 * Menu of server files
 *
 * Copyright (c) 2006-2018 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
require_once('controllers/component/menu_js.php');

class Onyx_Controller_Bo_Component_Server_Browser_Menu extends Onyx_Controller_Component_Menu_Js {
    
    /**
     * get tree
     */
     
    public function getTree($publish, $filter, $parent, $depth, $expand_all) {
            
        /**
         * set prefix
         */

        $directories = [];

        switch ($this->GET['scope'] ?? '') {
        
            case 'onyx':
                $directories = array(ONYX_DIR);
            break;
            case 'all':
                $directories = array(ONYX_DIR, ONYX_PROJECT_DIR);
            break;
            case 'project':
            default:
                $directories = array(ONYX_PROJECT_DIR);
            break;
        
        }
        
        /**
         * get list
         */
        
        $list = $this->getListForDirectories($directories);
        
        /**
         * trim extension
         */
         
        if ($this->GET['trim_extension'] ?? false) {
            foreach ($list as $k=>$item) {
                $list[$k]['name'] = preg_replace('/\.html$/', '', $list[$k]['name']);
                $list[$k]['name'] = preg_replace('/\.php$/', '', $list[$k]['name']);
                $list[$k]['id'] = preg_replace('/\.html$/', '', $list[$k]['id']);
                $list[$k]['id'] = preg_replace('/\.php$/', '', $list[$k]['id']);
                $list[$k]['parent'] = preg_replace('/\.html$/', '', $list[$k]['parent']);
                $list[$k]['parent'] = preg_replace('/\.php$/', '', $list[$k]['parent']);
            }
        }
        
        $tree = $this->buildTree($list, '');
        
        return $tree;
    }
    
    /**
     * getListForDirectories
     */
    
    public function getListForDirectories($directories) {
        
        if (!is_array($directories)) return false;
        
        /**
         * pass find param
         */
        
        switch ($this->GET['type'] ?? '') {
            case 'd':
                $find_param = '-type d';
            break;
            default:
                $find_param = '';
            break;
        }  
        
        /**
         * get list
         */
         
        require_once('models/common/common_file.php');
        $File = new common_file();

        $list = array();
        
        foreach ($directories as $directory_prefix) {
        
            $directory = $directory_prefix . $this->GET['directory'];

            if (file_exists($directory) && is_dir($directory)) $list_single = $File->getTree($directory, $find_param);
            else $list_single = array();

            $list = array_merge($list, $list_single);
            
        }
        
        if (is_array($list)) return $list;
        else return false;
        
    }

    /**
     * Is given node active? I.e. is it or its parent active?
     * Override if necessary
     */
    protected function isNodeActive(&$item)
    {
        $preg = str_replace("/", "\/", quotemeta($item['id']));
        return (preg_match("/{$preg}$/", $this->GET['open'] ?? $_SESSION['server_browser_last_open_folder'] ?? ''));
    }

    /**
     * Is given node open? Override if necessary
     */
    protected function isNodeOpen(&$item)
    {
        $preg = str_replace("/", "\/", quotemeta($item['id']));
        return (preg_match("/{$preg}/", $this->GET['open'] ?? $_SESSION['server_browser_last_open_folder'] ?? ''));
    }

}

