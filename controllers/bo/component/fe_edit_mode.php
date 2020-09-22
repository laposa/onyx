<?php
/**
 * Copyright (c) 2009-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Component_Fe_edit_Mode extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * get input
         */
         
        if ($_POST['fe_edit_mode']) {
            $mode = $_POST['fe_edit_mode'];
        } else if ($_GET['fe_edit_mode']) {
            $mode = $_GET['fe_edit_mode'];
        } else if ($_SESSION['fe_edit_mode']) {
            $mode = $_SESSION['fe_edit_mode'];
        } else {
            $mode = 'preview';
        }
        
        /**
         * safety check
         */
         
        if ($mode != 'preview' && $mode != 'edit' && $mode != 'move') $mode = 'preview';
        
        /**
         * save to session
         */
         
        $_SESSION['fe_edit_mode'] = $mode;
        
        /**
         * determine naked URL
         */
         
        $uri_strip = preg_replace('/\?.*$/', '', $_SESSION['uri']);
        
        /**
         * assign&parse variables to template
         */
         
        $this->tpl->assign('URI_STRIP', $uri_strip);
        $this->tpl->assign("SELECTED_$mode", 'selected="selected"');
        $this->tpl->assign("ACTIVE_$mode", 'active');
        $this->tpl->parse("content.fe_edit_mode_$mode");
        
        /**
         * optionally forward to stripped URL
         */
         
        if ($_GET['fe_edit_mode']) {
            onyxGoTo($uri_strip, 2);
        }
        
        return true;
    }
}

