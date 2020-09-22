<?php
/** 
 * Copyright (c) 2013-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Login extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * show twitter and facebook only if app ID is configured
         */
        
        /**
        if (ONYX_FACEBOOK_APP_ID) $this->tpl->parse('content.choose_login_type.facebook');
        if (ONYX_TWITTER_APP_ID) $this->tpl->parse('content.choose_login_type.twitter');
        
        if (ONYX_FACEBOOK_APP_ID || ONYX_TWITTER_APP_ID) $this->tpl->parse('content.choose_login_type');
        */
        
        //if (Onyx_Bo_Authentication::getInstance()->isAuthenticated()) msg('Authorised');
        //else msg('Not authorised');
        
        return true;
        
    }
}
