<?php
/**
 * Zend Search Index
 * Copyright (c) 2009-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('Zend/Http/Client.php');
require_once('Zend/Search/Lucene.php'); 
require_once('Zend/Search/Lucene/Document/Html.php'); 

class Onyx_Controller_Bo_Component_Search_Index extends Onyx_Controller { 
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * initiate
         */
         
        $this->index = $this->openIndex();
        
        /**
         * get info
         */
         
        $index_info = $this->getIndexInfo();

        $this->tpl->assign("INDEX_INFO", $index_info);
        
        /**
         * if requested, optimize index
         */
        
        if ($this->GET['optimize']) {
            msg("Index optimization executed");
            $this->index->optimize();
        }
        
        return true;
        
    }
    
    /**
     * get profile
     */
    
    public function getProfile() {
    
        if ($_SERVER['SSL_PROTOCOL'] || $_SERVER['HTTPS']) $protocol = 'https';
        else $protocol = 'http';

        $profile = array(
            'uri'           => "$protocol://{$_SERVER['SERVER_NAME']}/",
            'path'          => ONYX_PROJECT_DIR . 'var/index/',
        );
        
        return $profile;
    }
    
    /**
     * open index
     */
    
    public function openIndex() {
        
        $profile = $this->getProfile();
        
        // Open existing index
        try {
            $index = Zend_Search_Lucene::open($profile['path']);
        } catch(Exception $e) {
            msg("cannot open index", 'error');
            $index = false;
        }
        
        return $index;
    
    }
    
    /**
     * get index info
     */
     
    public function getIndexInfo() {
        
        if (is_object($this->index)) {
            $info['indexSize'] = $this->index->count();
            $info['documents'] = $this->index->numDocs();
        } else {
            $info = array();
        }
        
        return $info;
    }
    
    /**
     * optimize index
     */
    
    public function indexOptimize() {

        // Optimize index.
        $this->index->optimize();
    }
    
    /**
     * update document
     */
    
    public function updateDocument($uri) {
        
        $hits = $this->index->find('uri:' . $uri);
        
        foreach ($hits as $hit) {
            msg($hit->id . $hit->uri);
            //$this->index->delete($hit->id);
        }
    }
    
}
