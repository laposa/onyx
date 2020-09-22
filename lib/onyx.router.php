<?php
/** 
 * Copyright (c) 2009-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Router {

    var $request;
    
    var $modules;
    
    var $Onyx;
    
    /**
     * Construct
     */

    public function __construct($request = false)
    {
        if ($request) return $this->processAction($request);
    }
    
    /**
     * Process Action
     * return Onyx object
     */
    
    function processAction($request) {
        
        /**
         * expand request and check modules list
         */
         
        if ($this->request = $this->setRequest($request)) $modules = $this->_explodeRequest($this->getRequest());
        
        if (!is_array($modules)) {
            
            die("Onyx_Router: Can't explodeRequest " . htmlspecialchars($request));
        
        }
        
        $this->setModules($modules);

        /**
         * Initialise Onyx object(s)
         */
        
        if (count($modules) > 1) {
        
            $descendants = array_reverse($modules);
            foreach ($descendants as $key=>$descendant) {
                
                if ($key > 0) {
                    $subOnyx = $_Onyx[$key - 1];
                } else {
                    $subOnyx = false;
                }
                
                $classname = $this->_prepareCallBack($descendant);
                $_Onyx[$key] = new $classname($descendant, $subOnyx);
            }
            
            $this->Onyx = $_Onyx[count($descendants) - 1];
            
        } else {
        
            $classname = $this->_prepareCallBack($modules[0]);
            $this->Onyx = new $classname($modules[0]);
        
        }
        
        return $this->Onyx;
    }
    
    /**
     * prepare CallBack function
     */
    
    private function _prepareCallBack($request) {
    
        $file = preg_replace("/([A-Za-z0-9_\/]*).*/", "\\1", $request);
        if (file_exists(ONYX_DIR . "controllers/{$file}.php") || file_exists(ONYX_PROJECT_DIR . "controllers/{$file}.php")) {
            require_once("controllers/{$file}.php");
            $classname = "Onyx_Controller_" . preg_replace("/\//", "_", $file);
        } else {
            $classname = "Onyx_Controller";
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
        if (count($modules) > 20) trigger_error('Onyx: too many modules in one request', E_USER_ERROR);

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
