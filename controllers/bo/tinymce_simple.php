<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Tinymce_Simple extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        $tinymce['edit_elements'] = isset($this->GET['edit_elements']) ? ($this->GET['edit_elements'] == '' ? 'edit-content' : $this->GET['edit_elements']) : '';
        $this->tpl->assign('TINYMCE', $tinymce);
        return true;
    }
}
