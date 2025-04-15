<?php
/** 
 * Copyright (c) 2013-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Login extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        //if (Onyx_Bo_Authentication::getInstance()->isAuthenticated()) msg('Authorised');
        //else msg('Not authorised');
        
        return true;
        
    }
}
