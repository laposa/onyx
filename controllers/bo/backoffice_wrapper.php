<?php
/** 
 * Copyright (c) 2006-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Backoffice_Wrapper extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        header('X-Frame-Options: SAMEORIGIN');

        return true;
    }
}
