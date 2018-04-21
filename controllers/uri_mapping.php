<?php
/** 
 * Copyright (c) 2006-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Uri_Mapping extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        /**
         * first make sure we are on correct domain and using HTTPS if available
         */
         
        self::checkForSecurityRedirects();

        /**
         * input data
         */
        
        $translate = trim($this->GET['translate']);
        if ($translate != "/") $translate = rtrim($translate, '/');
        
        if ($this->GET['controller_request']) $controller_request = trim($this->GET['controller_request']);
        
        /**
         * file stored rules
         */
         
        if ($custom_translate = $this->proccessFileRules($translate)) {
            
            $controller_request = $custom_translate;
            $translate = false;
            
            //force login when request is from bo/ folder
            //similar check is done in Onxshop_Bootstrap
            if (preg_match('/bo\//', $controller_request)) {
                
                if (!$_SERVER['HTTPS'] && ONXSHOP_EDITOR_USE_SSL) {
                    header("Location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
                    exit;
                }
                
                $auth = Onxshop_Bo_Authentication::getInstance()->login();
                if (!$auth) $controller_request = 'sys/401';
                // don't allow to save this request to the cache
                Zend_Registry::set('omit_cache', true);
            }
            
        }
        
        /**
         * initialize database stored
         */
         
        require_once('models/common/common_uri_mapping.php');
        $this->Mapper = new common_uri_mapping();
        
        /**
         * translate request to $action_to_process
         */
         
        if ($translate) {
            
            if (is_numeric($node_id = trim($translate, '/'))) { // URL like /1234
                
                /**
                 * short URL redirects
                 * TODO: allow to pass GET parameters
                 */
                 
                $this->redirectToSeoURLAndExit($node_id);
                
            } else if (preg_match('/^\/\b(page|node)\b\/([0-9]*)$/', $translate, $match)) { // URL like /page/1234 or /node/1234
                
                $mapped_node_id = $match[2];
                $action_to_process = $this->getActionToProcessForExistingPage($mapped_node_id);
                
            } else if ($mapped_node_id = $this->Mapper->translate($translate)) { // URL like /abc-cbs
            
                $action_to_process = $this->getActionToProcessForExistingPage($mapped_node_id);
                
            } else if ($redirect_uri = $this->Mapper->getRedirectURI($translate)) { // URL like /abc-cbs
                
                /**
                 * explicit redirects
                 */
                
                $this->redirectToSeoURLAndExit($redirect_uri['node_id']);
            
            } else if ($translate == '/') { // root folder
                
                $action_to_process = $this->getActionToProcessForExistingPage($this->Mapper->conf['homepage_id']);
                
            } else {
                
                /**
                 * page not found
                 */
                 
                $action_to_process = $this->Mapper->getRequest($this->Mapper->conf['404_id']);
                
            }
            
        } else if ($controller_request) {
        
            // used for /request/ and /api/ handling to allow translating URLs
            $action_to_process = $controller_request;
        }
        
        /**
         * process
         */
        
        if ($action_to_process) {
        
            $page_data = $this->processMappedAction($action_to_process);
                    
            /**
             * URI mapping iself will become output of mapped page
             */
             
            $this->content = $page_data['content'];

        } else {
            
            msg("Cannot find action to process", 'error');
            
        }
        
        return true;
    }
    
    /**
     * redirectToSeoURL
     */
    
    public function redirectToSeoURLAndExit($node_id) {
        
        if (!is_numeric($node_id)) return false;
        
        $seo_redirect_uri = $this->Mapper->stringToSeoUrl("/page/{$node_id}");
        header("Location: $seo_redirect_uri", true, 301);
        exit;
    }
    
    /**
     * getActionToProcessForExistingPage
     */
     
    public function getActionToProcessForExistingPage($node_id) {
        
        if (!is_numeric($node_id)) return false;
        
        //save node_id to last record in history
        $_SESSION['orig'] = "/page/$node_id";
        $_SESSION['history'][count($_SESSION['history'])-1]['node_id'] = $node_id;
        
        $action_to_process = $this->Mapper->getRequest($node_id);
        
        return $action_to_process;
    }
     
    /**
     * processMappedAction
     */
     
    public function processMappedAction($action_to_process) {

        /**
         * process action
         */
        
        $Onxshop_Router = new Onxshop_Router();
        
        $Onxshop = $Onxshop_Router->processAction($action_to_process);
        
        if (is_object($Onxshop)) $page_data['content'] = $Onxshop->getContent();
        
        if ($page_data['content'] == "") $page_data['content'] = $this->content;

        return $page_data;
    }

    /**
     * proccessFileRules
     */
     
    public function proccessFileRules($translate) {
        
        $uri_map = $this->getFileRules();
        
        $apply = $this->proccessFileRulesItems($translate, $uri_map);
        
        $parsed = parse_url($apply);
        parse_str($parsed['query'], $query);
        
        foreach ($query as $k=>$item) {
            $_GET[$k] = $item;
        }
        
        if (array_key_exists('controller_request', $query)) return $query['controller_request'];
        else return $query['request'];
        
    }
    
    /**
     * proccessFileRulesItems
     */
     
    public function proccessFileRulesItems($translate, $uri_map) {
        
        if (!is_array($uri_map)) return false;
        
        foreach ($uri_map as $rule=>$apply) {
        
            $rule = str_replace('/', '\/', $rule);
            $rule = str_replace('\\\\', '\\', $rule);
        
            if (preg_match("/$rule/", $translate, $matches)) {
        
                if (is_array($apply)) {
                    $apply = $this->proccessFileRulesItems($translate, $apply);
                }
                
                foreach ($matches as $k=>$v) {
                    if ($k > 0) $apply = str_replace('$' . $k, $v, $apply);
                }
                
                return $apply;
            }
        }
    }
    
    
    /**
     * getRewriteRules
     */
     
    public function getFileRules() {
        
        require_once(ONXSHOP_DIR . 'conf/uri_map.php');
        if (file_exists(ONXSHOP_PROJECT_DIR . 'conf/uri_map.php')) require_once(ONXSHOP_PROJECT_DIR . 'conf/uri_map.php');
        return $uri_map;

    }

    /**
     * checkForSecurityRedirects
     */
     
    static function checkForSecurityRedirects() {
        
        if (defined('ONXSHOP_HSTS_ENABLE') && ONXSHOP_HSTS_ENABLE === true) header("Strict-Transport-Security: max-age=" . ONXSHOP_HSTS_TTL);
        
        /**
         * check main domain
         */
    
        if (defined('ONXSHOP_MAIN_DOMAIN')) {
            if (array_key_exists('HTTPS', $_SERVER)) $protocol = 'https';
            else $protocol = 'http';
            
            if ($_SERVER['HTTP_HOST'] != ONXSHOP_MAIN_DOMAIN) {
                Header( "HTTP/1.1 301 Moved Permanently" );
                Header( "Location: $protocol://" . ONXSHOP_MAIN_DOMAIN . "{$_SERVER['REQUEST_URI']}" );
                //exit the application immediately 
                exit;
            }
        }

        /**
         * force SSL
         */
        
        if (!($_SERVER['SSL_PROTOCOL'] || $_SERVER['HTTPS']) && ONXSHOP_CUSTOMER_USE_SSL) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
            exit;
        }
        
    }
}   
    
