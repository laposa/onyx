<?php
/**
 * Copyright (c) 2013-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

use Symfony\Contracts\Cache\ItemInterface;

require_once 'lib/facebook/autoload.php';
require_once 'lib/facebook/Helpers/FacebookCanvasHelper.php';
require_once 'lib/facebook/Helpers/FacebookRedirectLoginHelper.php';

class Onyx_Controller_Component_Client_Facebook extends Onyx_Controller {

    /**
     * main action
     */
    public function mainAction() {
        $this->commonAction();
        return true;
    }

    /**
     * common action
     */
    public function commonAction() {
        $this->catchFacebookErrorsViaGET();
        $this->initFacebook();
        return true;
    }

    /**
     * initFacebook
     */
    public function initFacebook() {
        // conf in deployment.php
        $this->facebook_conf = [
            'app_id'     => ONYX_FACEBOOK_APP_ID,
            'app_secret' => ONYX_FACEBOOK_APP_SECRET,
        ];

        $this->Facebook = new Facebook\Facebook($this->facebook_conf);
    }

    /**
     * Invoke the Graph API.
     */
    public function makeOpenGraphApiCall($params = []) {
        try {
            return $this->Facebook->request('GET', $$params['method'], $params);
        } catch (FacebookApiException $e) {
            msg("FB: " . $e->getMessage(), 'error', 1);
            return null;
        }
    }


    public function makeApiCall(/* polymorphic */) {
        $args = func_get_args();
        return call_user_func_array([$this, 'makeOpenGraphApiCall'], $args);
    }

    /**
     * callApiCached
     */
    public function makeApiCallCached(/* polymorphic */) {
        $args = func_get_args();

        /** @var Symfony\Component\Cache\Adapter\TagAwareAdapter $cache */
        $cache = Zend_registry::get('onyx_cache');
        $cacheKey = "Facebook_{$_SESSION['client']['customer']['facebook_id']}_" . md5(serialize($args));

        $data = $cache->get($cacheKey, function (ItemInterface $item) use ($args) {
            $response = call_user_func_array([$this, 'makeOpenGraphApiCall'], $args);
            if (is_null($response)) $item->expiresAfter(1);
            return $response;
        });

        return $data;
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
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            msg('Graph returned an error: ' . $e->getMessage(), 'error');
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            msg('Facebook SDK returned an error: ' . $e->getMessage(), 'error');
        }

        if ($response) $user = $response->getGraphUser();
        return $user;
    }
}
