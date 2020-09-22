<?php
/** 
 * Copyright (c) 2013-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once 'controllers/component/client/twitter.php';


class Onyx_Controller_Component_Client_Twitter_Auth extends Onyx_Controller_Component_Client_Twitter {

    /**
     * mainAction
     */
    
    public function mainAction() {
        
        $token = $this->commonAction();
        
        // verifyCredentials() tests if supplied user credentials are valid with minimal overhead.
        if ($token && $this->twitter->isAuthorised()) {
            
            $user_profile = $this->twitterCall('accountVerifyCredentials');
            
            if ($user_profile) {
                
                $this->tpl->assign('USER_PROFILE', $user_profile);
                
                if (is_numeric($user_profile->id)) {
                    //try to login
                    $this->loginToOnyx($user_profile);
                }
                
            } else {
                
                //don't cache the actual controler
                return false;
                
            }
            
            $this->tpl->parse('content.authorised');
            
        } else {
            
            $this->tpl->parse('content.login');
        }
        
        return true;
    }
    
    /**
     * loginToOnyx
     */
     
    public function loginToOnyx($user_profile) {
        
        require_once('models/client/client_customer.php');
        $Customer = new client_customer();
        $Customer->setCacheable(false);
        
        if ($customer_detail = $Customer->getUserByTwitterId($user_profile->id)) {
        
            //already exists a valid account, we can login
            msg("{$customer_detail['email']} is already registered", 'ok', 1);
            $_SESSION['client']['customer'] = $customer_detail; 
            $_SESSION['use_page_cache'] = false;
            
            // update profile image
            if ($customer_detail['profile_image_url'] != $user_profile->profile_image_url_https) {
                
                $customer_detail['profile_image_url'] = $user_profile->profile_image_url_https;
                
                $data_to_update = array();
                $data_to_update['id'] = $customer_detail['id'];
                $data_to_update['profile_image_url'] = $customer_detail['profile_image_url'];
                
                if ($Customer->updateCustomer($data_to_update)) msg("Updated profile image from Twitter");
                else msg("Profile image is different, but update failed", 'error');
                
            }
            
            // auto login (TODO allow to enable/disable this behaviour)
            $Customer->generateAndSaveOnyxToken($customer_detail['id']);
            
        } else {
        
            msg("Twitter ID {$user_profile->id} sucessfully authorised, but must register locally", 'ok', 1);
            
            //forward to registration
            $this->mapUserToOnyx($user_profile);
            onyxGoTo("/page/13");//TODO get node_id from conf
        
        }
    }
    
    /**
     * mapUserToOnyx
     */
     
    public function mapUserToOnyx($user_profile) {
        
        //map to Onyx schema
        $onyx_client_customer = array();
        $name = explode(" ", $user_profile->name);
        
        $onyx_client_customer['first_name'] = $name[0];
        $onyx_client_customer['last_name'] = $name[1];
        $onyx_client_customer['twitter_id'] = $user_profile->id;
        $onyx_client_customer['profile_image_url'] = $user_profile->profile_image_url_https;
        
        //save to session
        $_SESSION['r_client']['customer'] = $onyx_client_customer;
        
    }

}
