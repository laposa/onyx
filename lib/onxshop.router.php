<?php
/** 
 * Copyright (c) 2009-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Router {

    var $request;
    
    var $modules;
    
    var $Onxshop;
    
    /**
     * Construct
     */

    public function __construct($request = false)
    {
        if ($request) return $this->processAction($request);
    }
    
    /**
     * Process Action
     * return Onxshop object
     */
    
    function processAction($request) {
        
        /**
         * expand request and check modules list
         */
         
        if ($this->request = $this->setRequest($request)) $modules = $this->_explodeRequest($this->getRequest());
        
        if (!is_array($modules)) {
            
            die("Onxshop_Router: Can't explodeRequest " . htmlspecialchars($request));
        
        }
        
        $this->setModules($modules);

        /**
         * Initialise Onxshop object(s)
         */
        
        if (count($modules) > 1) {
        
            $descendants = array_reverse($modules);
            foreach ($descendants as $key=>$descendant) {
                
                if ($key > 0) {
                    $subOnxshop = $_Onxshop[$key - 1];
                } else {
                    $subOnxshop = false;
                }
                
                $classname = $this->_prepareCallBack($descendant);
                $_Onxshop[$key] = new $classname($descendant, $subOnxshop);
            }
            
            $this->Onxshop = $_Onxshop[count($descendants) - 1];
            
        } else {
        
            $classname = $this->_prepareCallBack($modules[0]);
            $this->Onxshop = new $classname($modules[0]);
        
        }
        
        return $this->Onxshop;
    }
    
    /**
     * prepare CallBack function
     */
    
    private function _prepareCallBack($request) {
    
        $file = preg_replace("/([A-Za-z0-9_\/]*).*/", "\\1", $request);
        if (file_exists(ONXSHOP_DIR . "controllers/{$file}.php") || file_exists(ONXSHOP_PROJECT_DIR . "controllers/{$file}.php")) {
            require_once("controllers/{$file}.php");
            $classname = "Onxshop_Controller_" . preg_replace("/\//", "_", $file);
        } else {
            $classname = "Onxshop_Controller";
        }
        
        return $classname;
    }
    
    
    /**
     * Router
     * @return array
     */
    function _explodeRequest($request) {

        /**
         * Parse request - explode modules
         */
         
        $modules= array();


        /**
         * get list of modules
         */
         
        $match_result = preg_match_all('/[^.~]*(~[^~]*~)?/i', $request, $match);

        foreach ($match[0] as $item) {
            if ($item != '') $modules[] = $item;    
        }

        //little protection against DOS
        if (count($modules) > 20) trigger_error('Onxshop: too many modules in one request', E_USER_ERROR);

        //print_r($modules);
        
        if (count($modules) == 0) return false;
        else return $modules;
    }
    
    
    /**
     * set modules
     *
     * @param unknown_type $modules
     * @return unknown
     */
     
    function setModules($modules) {
    
        if (is_array($modules)) {
            //we have descendants
            $this->modules = $modules;
            return true;
        } else {
            $this->modules = $modules;
        }
    
    }

    /**
     * get modules
     *
     * @return unknown
     */
    function getModules() {
    
        return $this->modules;
    
    }
    
    /**
     * set request
     *
     * @param unknown_type $request
     */
     
    public function setRequest($request) {

        if (preg_match('/[^a-z0-9\-\._\/\&\=\{\}\$\[\]|%@]&amp;+/i', $request)) {
        
            die('Invalid request: ' . htmlspecialchars($request));
        
        } else {
            
            return $request;
        }
    }

    /**
     * get request
     *
     * @return unknown
     */
    function getRequest() {
    
        return $this->request;
    
    }
    
}
