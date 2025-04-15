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
    
        $tinymce['mode'] = isset($this->GET['mode']) ? ($this->GET['mode'] == '' ? 'exact' : $this->GET['mode']) : '';
        $tinymce['edit_elements'] = isset($this->GET['edit_elements']) ? ($this->GET['edit_elements'] == '' ? 'edit-content' : $this->GET['edit_elements']) : '';
        $tinymce['relation'] = isset($this->GET['relation']) ? ($this->GET['relation'] == '' ? 'node' : $this->GET['relation']) : '';
        $tinymce['role'] = isset($this->GET['role']) ? ($this->GET['role'] == '' ? 'RTE' : $this->GET['role']) : '';
        $tinymce['theme'] = isset($this->GET['theme']) ? ($this->GET['theme'] == '' ? 'advanced' : $this->GET['theme']) : '';

        $this->tpl->assign('TINYMCE', $tinymce);

        return true;
    }
}
