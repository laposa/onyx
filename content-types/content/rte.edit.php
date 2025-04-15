<?php
/** 
 * Copyright (c) 2005-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/node/content/default.php');

class Onyx_Controller_Bo_Node_Content_Rte extends Onyx_Controller_Bo_Node_Content_Default {

    /**
     * post action
     */

    function post() {
        
        parent::post();
        
        /**
         * check hard link
         */
        
        $hard_links = $this->Node->findHardLinks($this->GET['id']);
        
        if (count($hard_links) > 0) {
            msg("Hard link detected, please fix.", 'error');
        }
        
        /*
        foreach ($hard_links as $hard_link) {
            $this->tpl->assign('ITEM', $hard_link);
            $this->tpl->parse('content.hard_links.item');
        }
        $this->tpl->parse('content.hard_links');
        */      
    }
}

