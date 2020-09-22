<?php
/** 
 * Copyright (c) 2013-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once 'lib/facebook/autoload.php';
require_once 'lib/facebook/Helpers/FacebookCanvasHelper.php';
require_once 'lib/facebook/Helpers/FacebookRedirectLoginHelper.php';

class Onyx_Controller_Component_Client_Facebook extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * call shared actions
         */
         
        $this->commonAction();

        
        return true;
        
    }
    
    /**
     * common action
     */
     
    public function commonAction() {
        
        /**
         * first catch errors
         */
         
        $this->catchFacebookErrorsViaGET();
        
        /**
         * initiate facebook object
         */
         
        $this->initFacebook();
                
        return true;
        
    }
    
    /**
     * initFacebook
     */
     
    public function initFacebook() {
        
        /**
         * conf in deployment.php
         */
         
        $this->facebook_conf = array(
            'app_id'  => ONYX_FACEBOOK_APP_ID,
            'app_secret' => ONYX_FACEBOOK_APP_SECRET
        );
        
        $this->Facebook = new Facebook\Facebook($this->facebook_conf);
        
    }

    /**
     * Invoke the Graph API.
     */
    
    public function makeOpenGraphApiCall($params = array()) {

        $args = func_get_args();

        try {
            return $this->Facebook->request('GET', $$params['method'], $params);

        } catch (FacebookApiException $e) {
        
            msg("FB: " . $e->getMessage(), 'error', 1);
            return null;
        }

    }


    public function makeApiCall(/* polymorphic */) {

        $args = func_get_args();

         return call_user_func_array(array($this, 'makeOpenGraphApiCall'), $args);
        

    }

    /**
     * callApiCached
     */
     
    public function makeApiCallCached(/* polymorphic */) {

        $args = func_get_args();

        // initialise cache
        require_once 'Zend/Cache.php';
        
        $frontendOptions = array(
        'lifetime' => ONYX_PAGE_CACHE_TTL,
        'automatic_serialization' => true
        );
        
        $backendOptions = array('cache_dir' => ONYX_PROJECT_DIR . 'var/cache/');
        $cache = Zend_Cache::factory('Output', 'File', $frontendOptions, $backendOptions);
        
        // create cache key
        $id = "Facebook_{$_SESSION['client']['customer']['facebook_id']}_" . md5(serialize($args));
        
        // attempt to read from cache
        if (is_array($data = $cache->load($id))) {
            
            //read from cache
            
            return $data;
        
        } else {
        
            // cache miss, make call and save to cache

            $response = call_user_func_array(array($this, 'makeOpenGraphApiCall'), $args);

            // save to cache
            if (!is_null($response)) $cache->save($response);
            
            return $response;
        
        }
        
        
    }
    
    /**
     * catchFacebookErrorsViaGET
     * as they come via GET callback
     */
     
    public function catchFacebookErrorsViaGET() {
        
        if (array_key_exists('error_reason', $this->GET)) msg("error_reason: {$this->GET['error_reason']}", 'error', 1);
        if (array_key_exists('error', $this->GET)) msg("error: {$this->GET['error']}", 'error', 1);
        if (array_key_exists('error_description', $this->GET)) msg("error_description: {$this->GET['error_description']}", 'error', 1);
        if (array_key_exists('state', $this->GET)) msg("state: {$this->GET['state']}", 'error', 1);
        
    }
    
    /**
     * getUser
     */
     
    public function getUser() {
        
        $helper = $this->Facebook->getCanvasHelper();
        $accessToken = $helper->getAccessToken();
        
        if (!$accessToken) return false;
        
        try {
          // Returns a `Facebook\FacebookResponse` object
          $response = $this->Facebook->get('/me?fields=id,name,first_name,last_name,email,gender', $accessToken);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          msg('Graph returned an error: ' . $e->getMessage(), 'error');
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          msg('Facebook SDK returned an error: ' . $e->getMessage(), 'error');
        }
        
        if ($response) $user = $response->getGraphUser();
        
        return $user;
        
    }
    
}
