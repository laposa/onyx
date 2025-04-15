<?php
/** 
 * Copyright (c) 2006-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Backoffice extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        //force SSL
        if ((!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS']) && ONYX_EDITOR_USE_SSL) {
            header("Location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
        }

        return true;
    }
}
