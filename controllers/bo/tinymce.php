<?php
/** 
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Tinymce extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        if ($this->GET['mode'] == '') $tinymce['mode'] = 'exact';
        else $tinymce['mode'] = $this->GET['mode'];
        
        if ($this->GET['edit_elements'] == '') $tinymce['edit_elements'] = 'edit-content';
        else $tinymce['edit_elements'] = $this->GET['edit_elements'];
        
        if ($this->GET['relation'] == '') $tinymce['relation'] = 'node';
        else $tinymce['relation'] = $this->GET['relation'];
        
        if ($this->GET['role'] == '') $tinymce['role'] = 'RTE';
        else $tinymce['role'] = $this->GET['role'];
        
        if ($this->GET['theme'] == '') $tinymce['theme'] = 'advanced';
        else $tinymce['theme'] = $this->GET['theme'];
        
        $this->tpl->assign('TINYMCE', $tinymce);
        
        //hack for ajax form saving
        if (!$_POST['save']) {
            $this->tpl->parse("content.init");
        }

        return true;
    }
}
