<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component.php');

class Onyx_Controller_Bo_Component_X extends Onyx_Controller_Bo_Component {

    function parseTemplate() {
        if (isset($_GET['edit']) && $_GET['edit'] == 'true') {
            $this->tpl->parse("content.edit");
        } else {
            $this->tpl->parse("content.preview");
        }
    }
}   

