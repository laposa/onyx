<?php
/**
 * Copyright (c) 2005-2018 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Bootstrap {

    public $Onxshop;
    
    public $headers;
    
    public $output;
    
    /**
     * constructor
     *
     * @return OnxshopBootstrap
     */
     
    function __construct() {
        
        /**
         * Include default libraries
         */
         
        require_once('xtemplate.class.php');
        require_once('controller.php');
        require_once('onxshop.request.php');
        require_once('model.php');
        require_once('onxshop.router.php');
        require_once('onxshop.bo.authentication.php');
        require_once('Zend/Db.php');
        require_once('Zend/Registry.php');
        require_once('Zend/Cache.php');

        /**
         * Initialise database connection object
         */

        $this->initDatabase();
        
        /**
         * Initialise cache backend connection
         */
         
        $this->initCache();
    
        /**
         * Initialise session
         */
        if ($this->isSessionRequired()) {
            
            $this->initSession();
            
        }
        
        /**
         * csrfCheck
         */
         
        if (ONXSHOP_CSRF_PROTECTION_ENABLED) $this->csrfCheck();
    
        /**
         * Disable DB cache when logged in as editor
         */
         
        if (Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
            define('ONXSHOP_DB_QUERY_CACHE', false);
        } else {
            define('ONXSHOP_DB_QUERY_CACHE', true);
        }
        
        /**
         * Initialise site configuration
         */

        $GLOBALS['onxshop_conf'] = $this->initConfiguration();

        /**
         * Initialise A/B testing
         */
        if (defined('ONXSHOP_ENABLE_AB_TESTING') && ONXSHOP_ENABLE_AB_TESTING == true) {
            if  ($_SESSION['ab_test_group'] !== 0 && $_SESSION['ab_test_group'] !== 1)
                $_SESSION['ab_test_group'] = mt_rand(0, 1);
        }
    
        //hack
        if ($_GET['logout'] == 1) {
            Onxshop_Bo_Authentication::getInstance()->logout();
            header("Location: http://{$_SERVER['SERVER_NAME']}/");
            exit;
        }
    
    }


    /**
     * Initialise database connection
     */
     
    function initDatabase() {

        /**
         * determine adapter name
         */
         
        switch (ONXSHOP_DB_TYPE) {
            case 'mysql':
                $adapter_name = 'Pdo_Mysql';
            break;
            case 'pgsql':
            default:
                $adapter_name = 'Pdo_Pgsql';
            break;
        }
        
        /**
         * set connection options
         */
        
        $connection_parameters =  array(
            'host'     => ONXSHOP_DB_HOST,
            'username' => ONXSHOP_DB_USER,
            'password' => ONXSHOP_DB_PASSWORD,
            'dbname'   => ONXSHOP_DB_NAME,
            'port'     => ONXSHOP_DB_PORT,
            'charset'  => 'UTF8'
        );
    
        /**
         * connect
         */
         
        try {
            $db = Zend_Db::factory($adapter_name, $connection_parameters);
            $db->getConnection();
        }  catch (Zend_Db_Adapter_Exception $e) {
            // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
            // perhaps factory() failed to load the specified Adapter class
        }
        
        /**
         * check connection
         */
         
        if (!$db->isConnected()) {
            header("HTTP/1.1 503 Service Unavailable");
            die('Our site is temporarily unavailable, please try again later.');
        }
        
        /**
         * profiler
         */
         
        if (ONXSHOP_IS_DEBUG_HOST) {
            $db->getProfiler()->setEnabled(true);
        }
        
        /**
         * store in registry
         */
         
        Zend_Registry::set('onxshop_db', $db);
        
    }
    
    /**
     * initCache
     */
    
    function initCache() {
        
        /**
         * check directory exists
         */
        
        if (!is_dir(ONXSHOP_PAGE_CACHE_DIRECTORY)) {
            if (!mkdir(ONXSHOP_PAGE_CACHE_DIRECTORY)) die(ONXSHOP_PAGE_CACHE_DIRECTORY . ' directory is not writeable');
        }
        
        /**
         * database cache
         */
         
        $frontendOptions = array(
        'lifetime' => ONXSHOP_DB_QUERY_CACHE_TTL,
        'automatic_serialization' => false
        );
        $backendOptions = array('cache_dir' => ONXSHOP_DB_QUERY_CACHE_DIRECTORY);
        
        $cache = Zend_Cache::factory('Core', ONXSHOP_DB_QUERY_CACHE_BACKEND, $frontendOptions, $backendOptions);
        
        /**
         * store db cache in registry
         */
         
        Zend_Registry::set('onxshop_db_cache', $cache);
        
        /**
         * page cache
         */
        
        $frontendOptions = array(
        'lifetime' => ONXSHOP_PAGE_CACHE_TTL,
        'automatic_serialization' => true
        );
        
        $backendOptions = array('cache_dir' => ONXSHOP_PAGE_CACHE_DIRECTORY);
        
        $this->cache = Zend_Cache::factory('Output', ONXSHOP_PAGE_CACHE_BACKEND, $frontendOptions, $backendOptions);
        
        /**
         * store page cache in registry
         */
         
        Zend_Registry::set('onxshop_page_cache', $this->cache);
    }

    /**
     * Initialise configuration from database
     */
     
    function initConfiguration() {
    
        $conf = array();

        require_once ('models/common/common_configuration.php');
        $Configuration = new common_configuration();
        
        $conf = $Configuration->getConfiguration();
        
        return $conf;
    }

    /**
     * Initialise session
     */
     
    function initSession() {
    
        /**
         * check directory exists
         */
         
        if (!is_dir(ONXSHOP_SESSION_DIRECTORY)) {
            if (!mkdir(ONXSHOP_SESSION_DIRECTORY)) die(ONXSHOP_SESSION_DIRECTORY . ' directory is not writeable');
        }
        
        switch (ONXSHOP_SESSION_TYPE) {
            case 'file':
                ini_set('session.save_path', ONXSHOP_SESSION_DIRECTORY);
            break;
    
            case 'database':
            default:
                require_once ('models/common/common_session.php');
    
                $Session = new common_session();
                $Session->setCacheable(false);
                $result = session_set_save_handler(array(&$Session, 'open'), array(&$Session, 'close'), array(&$Session, 'read'), array(&$Session, 'write'), array(&$Session, 'destroy'), array(&$Session, 'gc'));
                if (!$result) die("Can't init session!");
            break;
        }

        
        // change setting before starting the session
        session_name(ONXSHOP_SESSION_NAME);
        // disable no-cache headers
        //session_cache_limiter(0);
        //session_set_cookie_params(31536000);// = 3600 * 24 * 365
        session_start();
        //to be sure sessions are written before exit
        register_shutdown_function('session_write_close');
        //in PHP5.4 can be used this:
        //session_register_shutdown();
                
        if (!array_key_exists('active_pages', $_SESSION)) $_SESSION['active_pages'] = array(); // only pages
        if (!array_key_exists('full_path', $_SESSION)) $_SESSION['full_path'] = array(); // including layouts, containers, etc.
        if ($_SERVER['HTTP_X_FORWARDED_PROTO']) $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
        else if (array_key_exists('HTTPS', $_SERVER)) $protocol = 'https';
        else $protocol = 'http';

        $_SESSION['uri'] = "$protocol://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $_SESSION['last_item'] = $_SESSION['history'][count($_SESSION['history'])-1]['uri'];
        $_SESSION['orig'] = $_SERVER['REQUEST_URI'];
        
        
        $_SESSION['use_page_cache'] = $this->isPageCacheAllowed();

        // in session history we store only new page URIs,
        // exclude paths beginning with /ajax/, /request/, /popup/, /popupimage/, /view/
        if ($_SESSION['last_item'] != $_SESSION['uri'] && !preg_match('/^\/(ajax)*(request)*(popup)*(popupimage)*(view)*\//', $_SERVER['REQUEST_URI'])) {
            $uri = substr($_SESSION['uri'], 0, 2048); // prevent oversized database when request URI is very long i.e. under penetration test
            $_SESSION['history'][] = array('time'=>time(), 'uri'=>$uri);
        }

        $_SESSION['last_diff'] = $_SESSION['last_item'];

    }


    /**
     * User authentication
     */
    
    function processAuthentication($request) {
    
        if (!$_SERVER['HTTPS'] && ONXSHOP_EDITOR_USE_SSL) {
            header("Location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
            exit;
        }
        
        if (!Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {

            if (Onxshop_Bo_Authentication::getInstance()->login()) {
                
                msg('Successful Login to the backoffice', 'ok', 1);
            
            } else {
                
                msg('Login to the backoffice failed', 'error', 1);
                return false;
            
            }
            
        } else if (!Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
            
            Onxshop_Bo_Authentication::getInstance()->login();
            return false;
        
        }
        
        /**
         * deprecated since Onxshop 1.7
         */
        
        if ($_SESSION['client']['customer']['id'] < 1 && ONXSHOP_BACKOFFICE_REQUIRE_CUSTOMER_LOGIN) {
            
            $_SESSION['to'] = $_SERVER['REQUEST_URI'];
            
            $request = 'sys/html5.bo/backoffice_wrapper.bo/login';
            
        }
        
        return $request;
    }
    
    /**
     * check is authentication is required
     *
     * similar check is done in controllers/uri_mapping
     */
     
    public function isRequiredAuthentication($request) {
    
        $auth_is_required = false;
        
        // force login when request is from bo/ folder
        // similar check is also done in controllers/uri_mapping
        if (preg_match('/bo\//', $request)) {
            
            $auth_is_required = true;
            
        }
        
        //force login when controller_request in uri_mapping is from bo/ folder
        if ($_GET['controller_request']) {
            
            if (preg_match('/bo\//', $_GET['controller_request'])) $auth_is_required = true;
            
        }
        
        //force login when specified
        if (ONXSHOP_REQUIRE_AUTH && !ONXSHOP_IS_DEBUG_HOST) {
        
            $auth_is_required = true;
            
        }
        
        return $auth_is_required;
        
    }

    /**
     * Init pre action controllers
     *
     */
     
    function initPreAction($requests = array()) {
    
        foreach ($requests as $request) {
            $this->processAction($request);
        }
    }
    
    /**
     * Init Action
     */
    
    function initAction($request = 'uri_mapping') {

        if (!$request) $request = 'uri_mapping';
                
        /**
         * User authentication required for certain actions
         */
        
        if ($this->isRequiredAuthentication($request)) {
            
            $this->disable_page_cache = 1;
            
            if(!$request = $this->processAuthentication($request)) {
                $request = 'sys/xhtml.sys/401';
            }
        }
        
        /**
         * return cached version only if session cache is enabled and $disable_page_cache isn't set
         */
         
         
        if ($this->isPageCacheAllowed()) $this->processActionCached($request);
        else $this->processAction($request);

    }
    
    /**
     * Process Action
     */
    
    function processAction($request) {
    
        $router = new Onxshop_Router();
        
        $this->Onxshop = $router->processAction($request);
        
        $this->headers = $this->getPublicHeaders();
        $this->output = $this->Onxshop->finalOutput();

    }
    
    /**
     * page (snippet) output cache
     */
    
    function processActionCached($request) {
        
        // create cache key
        $id = preg_replace('/\W/', '', $_SERVER['HTTP_HOST']) . '_GET_' . md5(ONXSHOP_DB_HOST . ONXSHOP_DB_PORT . ONXSHOP_DB_NAME . $request . serialize($_GET) . isset($_SERVER['HTTPS'])); // include hostname and database connection details to prevent conflicts in shared cache engine environment
        if (defined('ONXSHOP_ENABLE_AB_TESTING') && ONXSHOP_ENABLE_AB_TESTING == true) $id .= $_SESSION['ab_test_group'];

        if (!is_array($data = $this->cache->load($id))) {
            // cache miss
            
            $this->processAction($request);
            
            if ($this->canBeSavedInCache()) {

                $data_to_cache = array();
                $data_to_cache['output_headers'] = $this->headers;
                $data_to_cache['output_body'] = $this->output;
                $this->cache->save($data_to_cache);

                // update index immediately if enabled in the configuration,
                // but not when search_query is submitted (don't index search results)
                // and not when forward "to" is provided
                // TODO: canonise the request before submitting for indexing
                if (ONXSHOP_ALLOW_SEARCH_INDEX_AUTOUPDATE && !array_key_exists('search_query', $_GET) && !array_key_exists('to', $_GET)) $this->indexContent($_GET['translate'], $this->output);
            }

        } else {
        
            $this->headers = $data['output_headers'];
            $this->output = $data['output_body'];
            $this->restoreHeaders();
        }
    }
    
    /**
     * Index with Zend_Lucene
     *
     * @param unknown_type $uri
     * @param unknown_type $htmlString
     */
     
    function indexContent($uri, $htmlString) {
    
        require_once('Zend/Search/Lucene.php');

        $index_location = ONXSHOP_PROJECT_DIR . 'var/index';
        
        if (is_dir($index_location)) {
            // Open existing index
            try {
                $index = Zend_Search_Lucene::open($index_location);
            } catch(Exception $e) {
                // Create index
                try {
                    $index = Zend_Search_Lucene::create($index_location);
                } catch(Exception $e) {
                    $index = false;
                }
            }
        }

        if ($index) {
            // find and remove pages with the same URI
            $hits = $index->find("uri:" . $uri);
            foreach ($hits as $hit) $index->delete($hit);
    
            $doc = Zend_Search_Lucene_Document_Html::loadHTML($htmlString, true);
            $doc->addField(Zend_Search_Lucene_Field::Keyword('uri', $uri));
            
            $index->addDocument($doc);
            $index->commit();
        }
    }
    
    /**
     * restoreHeaders
     * will resend original headers for cached pages
     */
     
    public function restoreHeaders() {
        
        if (is_array($this->headers)) {
        
            foreach ($this->headers as $header) {
                header($header);
            }
        }
    
        header("X-Onxshop-From-Cache: 1");
        
    }
    
    /**
     * getPublicHeaders
     * store in cache only public headers, definitely not "Set-Cookie: PHPSESSID=2vom6fgga2lp0cg5d9gspqomv0; path=/" header!!!
     */
     
    public function getPublicHeaders() {
    
        $all_headers = headers_list();
        $public_headers = array();
        
        foreach ($all_headers as $item) {
        
            if (
                preg_match('/^Content-Type/i', $item) || 
                preg_match('/^Access-Control-Allow-Origin/i', $item) || 
                preg_match('/^Strict-Transport-Security/i', $item)
            ) $public_headers[] = $item;
            
        }
        
        return $public_headers;
        
    }
    
    /**
     * getOutput
     */
     
    public function getOutput() {
    
        $result = $this->output;
        
        $result = $this->outputFilterGlobal($result);
        $result = $this->outputFilterPublic($result);
        
        //hack
        if ($Onxshop->http_status != '404') {
            if ($_SERVER['HTTP_REFERER'] != $_SESSION['uri'] && $_SERVER['HTTP_REFERER'] != '') {
                $_SESSION['referer'] = $_SERVER['HTTP_REFERER'];
            }
        } else {
            array_pop($_SESSION['history']);
            $_SESSION['last_diff'] = $_SESSION['history'][count($_SESSION['history'])-1]['uri'];
        }

        return $result;
    }
    
    /**
     * Final output content
     */
    
    function finalOutput() {
    
        $result = $this->getOutput();
        
        session_write_close();
        
        return $result;
    }

    /**
     * outputFilterGlobal
     */

    public function outputFilterGlobal($content) {

        require_once('models/common/common_uri_mapping.php');
        $Mapper = new common_uri_mapping();

        // translate /page/{ID} to URLs
        $content = $Mapper->system_uri2public_uri($content);

        // CDN rewrites for URLs
        if (ONXSHOP_CDN && (ONXSHOP_CDN_USE_WHEN_SSL || !isset($_SERVER['HTTPS']))) {
            require_once('lib/onxshop.cdn.php');
            $CDN = new Onxshop_Cdn();
            $content = $CDN->processOutputHtml($content);
        }
        
        // remove multiple white spaces beetween tags
        if (ONXSHOP_COMPRESS_OUTPUT == 1) {
            $content = preg_replace("/>[\s]+</","> <", $content);
        }

        return $content;
    }

    /**
     * Output filter for public clients (this filter should only apply when in frontend preview mode)
     */
     
    public function outputFilterPublic($content) {
        
        /**
         * Substitute constants in the output for logged in users
         * TODO: highlight in documentation!
         */
        
        //only when not logged in backoffice
        if (!Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
            if ($_SESSION['client']['customer']['id'] > 0) {
                $content = preg_replace("/{{customer.first_name}}/", htmlspecialchars($_SESSION['client']['customer']['first_name']), $content);
            } else {
                //assign empty string
                $content = preg_replace("/{{customer.first_name}}/", '', $content);
            }
        }

        // translations
        if (ONXSHOP_SIMPLE_TRANSLATION_ENABLED && !ONXSHOP_IN_BACKOFFICE) {

            $locale = $_SESSION['locale'];
            $default_locale = $GLOBALS['onxshop_conf']['global']['locale'];

            if ($locale != $default_locale) {
                require_once('models/international/international_translation.php');
                $Translation = new international_translation();
                $node_id = $_SESSION['last_item'] = $_SESSION['history'][count($_SESSION['history'])-1]['node_id'];
                $content = $Translation->translatePage($content, $locale, $node_id);
            }
        }
        
        return $content;
    }
    
    /**
     * csrfCheck
     */
     
    public function csrfCheck() {
        
        /**
         * generate
         */
         
        $CSRF_TOKEN = hash_hmac('md5', session_id(), ONXSHOP_ENCRYPTION_SALT);
        
        /**
         * save
         */
         
        Zend_Registry::set('CSRF_TOKEN', $CSRF_TOKEN);
        
        /**
         * check if POST data are submitted
         * for testing period limited only to backoffice users
         */
         
        if (Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
            
            if (count($_POST) > 0) {
                
                if ($CSRF_TOKEN !== $_POST['csrf_token']) {
                    $error_message = 'CSRF_TOKEN is not valid';
                    echo $error_message;
                    trigger_error($error_message, E_USER_ERROR);
                    die();
                }
                
            }
        }
    }
    
    /**
     * isSessionRequired
     */
     
    public function isSessionRequired() {
        
        /**
         * exceptions
         */
         
        $exceptions = array('/request/component/ecommerce/roundel_image');
        
        if (array_key_exists('translate', $_GET) && in_array($_GET['translate'], $exceptions)) return false;
        
        /**
         * don't need session for cached pages when client doesn't have PHPSESSID
         */
        
        //if ($this->isPageCacheAllowed() && !isset($_COOKIE['PHPSESSID'])) return false;
        
        return true;
        
    }
    
    /**
     * isPageCacheAllowed
     */
    
    public function isPageCacheAllowed() {
        
        /**
         * default value
         */
         
        $use_page_cache = true;

        /**
         * cache can be disabled on request
         */
        
        if (isset($_GET['nocache'])) $this->disable_page_cache = $_GET['nocache'];
        
        // check if explicitly disabled
        if ($this->disable_page_cache || ONXSHOP_PAGE_CACHE_TTL == 0) {
            
            $use_page_cache = false;
        
        } else {
        
            /**
             * previously set (i.e. disabled) in session
             */
         
            if (isset($_SESSION['use_page_cache'])) $use_page_cache = $_SESSION['use_page_cache'];
    
            /**
             * disable page cache for whole session after a user interaction and for backoffice users
             */
             
            if (count($_POST) > 0 || Onxshop_Bo_Authentication::getInstance()->isAuthenticated() || $_SESSION['client']['customer']['id'] > 0) $use_page_cache = false;
            
            /**
             * TODO: allow to configure what _GET variables will disable page cache
             * disable page cache also when sorting and mode is submitted
             * component/ecommerce/product_list_sorting
             * or when preview_token is used, i.e. news article preview
             */

            if (is_array($_GET['sort']) || $_GET['product_list_mode'] || 
                $_GET['preview_token'] || $_GET['preview_token'] || $_GET['nocache_session']) $use_page_cache = false;

        }
        
        return $use_page_cache;
        
    }
    
    /**
     * canBeSavedInCache
     */
     
    public function canBeSavedInCache() {
        
        if (
            Zend_Registry::isRegistered('controller_error')
            || Zend_Registry::isRegistered('omit_cache')
            || ($_SESSION['use_page_cache'] == false)
            ) {
            
            return false;
        
        } else {
            
            return true;
        
        }
        
    }
}
