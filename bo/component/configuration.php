<?php
/**
 * 
 * Copyright (c) 2009-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Component_Configuration extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        return $this->standardConfAction();
        
    }
    
    /**
     * standard conf action
     */
     
    public function standardConfAction() {
        
        $this->initializeConfiguration();
        
        if ($_POST['save'] && is_array($_POST['conf']['item'])) {
        
            $conf = $this->prepareForSave($_POST['conf']);
        
            $this->saveConfiguration($conf);
        }
        
        $conf = $this->listConfiguration();
        
        $conf = $this->prepareForDisplay($conf);
        
        $this->displayConf($conf);

        return true;
    }

    
    /**
     * initialize
     */
    
    function initializeConfiguration() {
        
        require_once('models/common/common_configuration.php');
        $this->Configuration = new Common_configuration();
    }
    
    /**
     * save
     */
    
    function saveConfiguration($conf) {
        
        if (is_array($conf)) {
        
            msg("Saving config");
            
            foreach ($conf['item'] as $property=>$value) {
                
                if ($this->Configuration->saveConfig($conf['object'], $property, $value, $conf['node_id'])) {
                    msg("Saved $property $value");
                }
            }
        }
    }
        
    /**
     * prepare for save
     */
    
    function prepareForSave($conf) {
    
            return $conf;
    }
    
    /**
     * list and print
     */
    
    function listConfiguration() {  
    
        $conf = $this->Configuration->getConfiguration();
        
        return $conf;
    
    }
    
    /**
     * display
     */
    
    function displayConf($conf) {
    
        $this->tpl->assign("CONF", $conf);
        return true;
    }
    
    /**
     * prepare for display
     */
     
    function prepareForDisplay($conf) {
        
        return $conf;
    }
}
