<?php
/**
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/configuration.php');

class Onxshop_Controller_Bo_Component_Configuration_Global extends Onxshop_Controller_Bo_Component_Configuration {

    /**
     * main action
     */
     
    public function mainAction() {
        
        return $this->standardConfAction();
        
    }
    
    /**
     * prepare for save
     */
    
    function prepareForSave($conf) {
    
        if ($conf['item']['display_secondary_navigation'] == 'on' || $conf['item']['display_secondary_navigation'] == 1) $conf['item']['display_secondary_navigation'] = 1;
        else $conf['item']['display_secondary_navigation'] = 0;
        
        if ($conf['item']['display_content_side'] == 'on' || $conf['item']['display_content_side'] == 1) $conf['item']['display_content_side'] = 1;
        else $conf['item']['display_content_side'] = 0;
        
        if ($conf['item']['display_content_foot'] == 'on' || $conf['item']['display_content_foot'] == 1) $conf['item']['display_content_foot'] = 1;
        else $conf['item']['display_content_foot'] = 0;
        
        return $conf;
    }
    
    /**
     * prepare for display
     */
     
    public function prepareForDisplay($conf) {
    
    
        //DISPLAY SECONDARY NAVIGATION update on all pages
        //UPDATE common_node SET display_secondary_navigation  = 1 WHERE node_group = 'page' AND node_controller = 'default' AND id > 1000
        //specific to "global" object
        
        
        $selected[preg_replace("/\.UTF-8/", "", $conf['global']['locale'])] = "selected='selected'";
        $selected[$conf['global']['default_currency']] = "selected='selected'";

        $sorting = $conf['global']['product_list_sorting'];
        $selected['product_list_sorting_' . $sorting] = "selected='selected'";

        $this->tpl->assign("SELECTED", $selected);
        
        if ($conf['global']['display_secondary_navigation'] == 1) $CHECKED['display_secondary_navigation'] = 'checked="checked"';
        if ($conf['global']['display_content_side'] == 1) $CHECKED['display_content_side'] = 'checked="checked"';
        if ($conf['global']['display_content_foot'] == 1) $CHECKED['display_content_foot'] = 'checked="checked"';
        
        $this->tpl->assign("CHECKED", $CHECKED);
        
        
        return $conf;
    
    }
    
}
