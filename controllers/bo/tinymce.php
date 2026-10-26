<?php
/** 
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Tinymce extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $tinymce['edit_element'] = isset($this->GET['edit_element']) ? ($this->GET['edit_element'] == '' ? 'edit-content' : $this->GET['edit_element']) : '';
        $tinymce['relation'] = isset($this->GET['relation']) ? ($this->GET['relation'] == '' ? 'node' : $this->GET['relation']) : '';

        $this->tpl->assign('TINYMCE', $tinymce);

        return true;
    }
}
