<?php
/**
 * Copyright (c) 2005-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Contracts\Cache\ItemInterface;

require_once('lib/onyx.container.php');

class Onyx_Bootstrap {
    public $Onyx;
    public $headers;
    public $output;
    /** @var Symfony\Component\Cache\Adapter\TagAwareAdapter */
    public $cache;
    /** @var Memcached */
    protected $memcachedClient;
    /** @var Onyx_Container */
    protected $container;

    /**
     * constructor
     */
    public function __construct()
    {
        // Include default libraries
        require_once('xtemplate.class.php');
        require_once('controller.php');
        require_once('onyx.request.php');
        require_once('model.php');
        require_once('onyx.router.php');
        require_once('onyx.bo.authentication.php');

        // Get instance of dependency injection container
        $this->container = Onyx_Container::getInstance();

        // Initialise database connection object
        $this->initDatabase();

        // Initialise media library folder
        $this->initFiles();

        // Initialise cache backend connection
        $this->initCache();

        // Initialise session
        if (ONYX_SESSION_START_FOR_ALL_USERS || $this->isSessionRequired()) {
            $this->initSession();
        }

        // enable csrfCheck
        if (ONYX_CSRF_PROTECTION_ENABLED) $this->csrfCheck();

        // Disable DB cache when logged in as editor
        if (Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {
            define('ONYX_DB_QUERY_CACHE', false);
        } else {
            define('ONYX_DB_QUERY_CACHE', true);
        }

        // Initialise site configuration
        $GLOBALS['onyx_conf'] = $this->initConfiguration();

        // Initialise A/B testing
        if (defined('ONYX_ENABLE_AB_TESTING') && ONYX_ENABLE_AB_TESTING == true) {
            if ($_SESSION['ab_test_group'] !== 0 && $_SESSION['ab_test_group'] !== 1)
                $_SESSION['ab_test_group'] = mt_rand(0, 1);
        }

        //hack
        if ($_GET['logout'] == 1) {
            Onyx_Bo_Authentication::getInstance()->logout();
            header("Location: http://{$_SERVER['SERVER_NAME']}/");
            exit;
        }
    }

    /**
     * Initialise database connection
     */
    public function initDatabase()
    {
        // determine adapter name
        $adapterName = ONYX_DB_TYPE === 'mysql' ? 'pdo_mysql' : 'pdo_pgsql';

        // set connection options
        $connectionParams = [
            'host'     => ONYX_DB_HOST,
            'user'     => ONYX_DB_USER,
            'password' => ONYX_DB_PASSWORD,
            'dbname'   => ONYX_DB_NAME,
            'port'     => ONYX_DB_PORT,
            'charset'  => 'UTF8',
            'driver'   => $adapterName,
        ];

        // connect
        try {
            $db = DriverManager::getConnection($connectionParams);
            $db->connect();
        } catch (Exception $e) {
            msg($e->getMessage(), 'error');
        }

        // check connection
        if (!$db->isConnected()) {
            header("HTTP/1.1 503 Service Unavailable");
            die('Our site is temporarily unavailable, please try again later.');
        }

        // enable profiler
        if (ONYX_IS_DEBUG_HOST && (ONYX_DB_PROFILER || ONYX_TRACY_DB_PROFILER)) {
            require_once('lib/Doctrine/OnyxSQLLogger.php');
            $db->getConfiguration()->setSQLLogger(new OnyxSQLLogger());
        }

        // store in registry
        $this->container->set('onyx_db', $db);
    }

    /**
     * close database connection
     */
    public function closeDatabase()
    {
        $db = $this->container->get('onyx_db');
        $db->close();
    }

    /**
     * initFiles
     */
    public function initFiles()
    {
        // check if directory exists
        $directory = ONYX_PROJECT_DIR . 'var/files/';
        if (!is_dir($directory)) {
            if (!mkdir($directory)) die($directory . ' directory is not writeable');
        }
    }

    /**
     * initCache
     */
    public function initCache()
    {
        // check if directory exists
        if (!is_dir(ONYX_PAGE_CACHE_DIRECTORY)) {
            if (!mkdir(ONYX_PAGE_CACHE_DIRECTORY)) die(ONYX_PAGE_CACHE_DIRECTORY . ' directory is not writeable');
        }

        $dbCache = $this->initCacheAdapter(ONYX_DB_QUERY_CACHE_BACKEND, 'DB', ONYX_DB_QUERY_CACHE_TTL, ONYX_DB_QUERY_CACHE_DIRECTORY);
        $pageCache = $this->initCacheAdapter(ONYX_PAGE_CACHE_BACKEND, 'PAGE', ONYX_PAGE_CACHE_TTL, ONYX_PAGE_CACHE_DIRECTORY);
        $generalCache = $this->initCacheAdapter(ONYX_GENERAL_CACHE_BACKEND, 'GENERAL', ONYX_GENERAL_CACHE_TTL, ONYX_GENERAL_CACHE_BACKEND);

        $this->cache = $pageCache;
        $this->container->set('onyx_db_cache', $dbCache);
        $this->container->set('onyx_page_cache', $pageCache);
        $this->container->set('onyx_cache', $generalCache);
    }

    /**
     * Creates Memecached, Apc or File cache adapter
     * @param string $adapterType can be one of: Libmemcached, Apc, File (defaults to File)
     * @param string $namespace
     * @param integer $ttl
     * @param string $cacheDirectory
     * @return TagAwareAdapter
     */
    protected function initCacheAdapter($adapterType, $namespace, $ttl, $cacheDirectory = '')
    {
        if ($adapterType === 'Libmemcached') {
            $namespace = $namespace . "_" . preg_replace('/\W/', '', $_SERVER['HTTP_HOST']) . ONYX_DB_HOST . ONYX_DB_PORT . ONYX_DB_NAME;
            $adapter = new TagAwareAdapter(new MemcachedAdapter($this->getMemcachedClient(), $namespace, $ttl));
        } else if ($adapterType === 'Apc') {
            $adapter = new TagAwareAdapter(new ApcuAdapter($namespace, $ttl));
        } else {
            $adapter = new TagAwareAdapter(new FilesystemAdapter($namespace, $ttl, $cacheDirectory));
        }

        // disable stampede prevention by file locking (allows multiple processes to compute the same key)
        // when enabled some locks weren't released properly leaving hanging processes
        $adapter->setCallbackWrapper(null);
        return $adapter;
    }

    /**
     * Creates Memcached client for cache adapters
     * @return Memcached
     */
    protected function getMemcachedClient()
    {
        if (!$this->memcachedClient) {
            $this->memcachedClient = MemcachedAdapter::createConnection("memcached://" . ONYX_CACHE_BACKEND_LIBMEMCACHED_HOST . ":" . ONYX_CACHE_BACKEND_LIBMEMCACHED_PORT);
        }

        return $this->memcachedClient;
    }

    /**
     * Initialise configuration from database
     */
    public function initConfiguration()
    {
        require_once('models/common/common_configuration.php');
        $Configuration = new common_configuration();
        return $Configuration->getConfiguration();
    }

    /**
     * Initialise session
     */
    public function initSession()
    {
        // check if directory exists
        if (!is_dir(ONYX_SESSION_DIRECTORY)) {
            if (!mkdir(ONYX_SESSION_DIRECTORY)) die(ONYX_SESSION_DIRECTORY . ' directory is not writeable');
        }

        switch (ONYX_SESSION_TYPE) {
            case 'file':
                ini_set('session.save_path', ONYX_SESSION_DIRECTORY);
                break;

            case 'database':
            default:
                require_once('models/common/common_session.php');

                $Session = new common_session();
                $Session->setCacheable(false);
                $result = session_set_save_handler([&$Session, 'open'], [&$Session, 'close'], [&$Session, 'read'], [&$Session, 'write'], [&$Session, 'destroy'], [&$Session, 'gc']);
                if (!$result) die("Can't init session!");
                break;
        }

        // change setting before starting the session
        session_name(ONYX_SESSION_NAME);
        $current_cookie_params = session_get_cookie_params();

        if (onyxDetectProtocol() == 'https') $secure = true;
        else $secure = false;

        session_set_cookie_params($current_cookie_params['lifetime'], $current_cookie_params['path'], $current_cookie_params['domain'], $secure, $current_cookie_params['httponly']);

        // disable no-cache headers
        //session_cache_limiter(0);
        //session_set_cookie_params(31536000);// = 3600 * 24 * 365
        session_start();
        //to be sure sessions are written before exit
        register_shutdown_function('session_write_close');
        //in PHP5.4 can be used this:
        //session_register_shutdown();

        if (!array_key_exists('active_pages', $_SESSION)) $_SESSION['active_pages'] = []; // only pages
        if (!array_key_exists('full_path', $_SESSION)) $_SESSION['full_path'] = []; // including layouts, containers, etc.
        if ($_SERVER['HTTP_X_FORWARDED_PROTO']) $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
        elseif (array_key_exists('HTTPS', $_SERVER)) $protocol = 'https';
        else $protocol = 'http';

        $_SESSION['uri'] = "$protocol://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $_SESSION['orig'] = $_SERVER['REQUEST_URI'];


        $_SESSION['use_page_cache'] = $this->isPageCacheAllowed();

        // in session history we store only new page URIs,
        // exclude paths beginning with /ajax/, /request/, /popup/, /popupimage/, /view/
        if ($_SESSION['last_item'] != $_SESSION['uri'] && !preg_match('/^\/(ajax)*(request)*(popup)*(popupimage)*(view)*\//', $_SERVER['REQUEST_URI'])) {
            $uri = substr($_SESSION['uri'], 0, 2048); // prevent oversized database when request URI is very long i.e. under penetration test
            $_SESSION['history'][] = ['time' => time(), 'uri' => $uri];
        }

        $_SESSION['last_diff'] = $_SESSION['last_item'];
    }

    /**
     * close session
     */
    public function closeSession()
    {
        session_write_close();
    }

    /**
     * User authentication
     */
    public function processAuthentication($request)
    {
        if (!$_SERVER['HTTPS'] && ONYX_EDITOR_USE_SSL) {
            header("Location: https://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");
            exit;
        }

        if (!Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {
            if (Onyx_Bo_Authentication::getInstance()->login()) {
                msg('Successful Login to the backoffice', 'ok', 1);
            } else {
                msg('Login to the backoffice failed', 'error', 1);
                return false;
            }
        } elseif (!Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {
            Onyx_Bo_Authentication::getInstance()->login();
            return false;
        }

        /**
         * @deprecated since Onyx 1.7
         */
        if ($_SESSION['client']['customer']['id'] < 1 && ONYX_BACKOFFICE_REQUIRE_CUSTOMER_LOGIN) {
            $_SESSION['to'] = $_SERVER['REQUEST_URI'];
            $request = 'bo/backoffice_wrapper.bo/login';
        }

        return $request;
    }

    /**
     * check is authentication is required
     * similar check is done in controllers/uri_mapping
     */
    public function isRequiredAuthentication($request)
    {
        $auth_is_required = false;

        // force login when request is from bo/ folder
        // similar check is also done in controllers/uri_mapping
        if (preg_match('/bo\//', $request)) {
            $auth_is_required = true;
        }

        // force login when controller_request in uri_mapping is from bo/ folder
        if ($_GET['controller_request']) {
            if (preg_match('/bo\//', $_GET['controller_request'])) $auth_is_required = true;
        }

        // force login when specified
        if (ONYX_REQUIRE_AUTH && !ONYX_IS_DEBUG_HOST) {
            $auth_is_required = true;
        }

        return $auth_is_required;
    }

    /**
     * Init pre action controllers
     */
    public function initPreAction($requests = [])
    {
        foreach ($requests as $request) {
            $this->processAction($request);
        }
    }

    /**
     * Init Action
     */
    public function initAction($request = 'uri_mapping')
    {
        if (!$request) $request = 'uri_mapping';

        // User authentication required for certain actions
        if ($this->isRequiredAuthentication($request)) {
            $this->disable_page_cache = 1;

            if (!$request = $this->processAuthentication($request)) {
                $request = 'sys/401';
            }
        }

        // return cached version only if session cache is enabled and $disable_page_cache isn't set
        if ($this->isPageCacheAllowed()) $this->processActionCached($request);
        else $this->processAction($request);
    }

    /**
     * Process Action
     */
    public function processAction($request)
    {
        $router = new Onyx_Router();
        $this->Onyx = $router->processAction($request);
        $this->headers = $this->getPublicHeaders();
        $this->output = $this->Onyx->finalOutput();
    }

    /**
     * page (snippet) output cache
     */
    public function processActionCached($request)
    {
        // create cache key
        $id = 'GET_' . md5($request . serialize($_GET) . isset($_SERVER['HTTPS']));
        if (defined('ONYX_ENABLE_AB_TESTING') && ONYX_ENABLE_AB_TESTING == true) $id .= $_SESSION['ab_test_group'];
        if (!$this->canBeSavedInCache()) {
            $this->processAction($request);
            return;
        }

        $data = $this->cache->get($id, function (ItemInterface $item) use ($request) {
            $this->processAction($request);
            $dataToCache = ['output_headers' => $this->headers, 'output_body' => $this->output];

            // update index immediately if enabled in the configuration,
            // but not when search_query is submitted (don't index search results)
            // and not when forward "to" is provided
            // TODO: canonise the request before submitting for indexing
            if (ONYX_ALLOW_SEARCH_INDEX_AUTOUPDATE && !array_key_exists('search_query', $_GET) && !array_key_exists('to', $_GET)) {
                $this->indexContent($_GET['translate'], $this->output);
            }

            return $dataToCache;
        });

        $this->headers = $data['output_headers'];
        $this->output = $data['output_body'];
        $this->restoreHeaders();
    }

    /**
     * Index - consider removal
     *
     * @param string $uri
     * @param string $htmlString
     */
    public function indexContent($uri, $htmlString)
    {
        return false;

        if (is_dir($index_location)) {
            // Open existing index
            try {
                $index = Zend_Search_Lucene::open($index_location);
            } catch (Exception $e) {
                // Create index
                try {
                    $index = Zend_Search_Lucene::create($index_location);
                } catch (Exception $e) {
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
    public function restoreHeaders()
    {
        if (is_array($this->headers)) {
            foreach ($this->headers as $header) {
                header($header);
            }
        }

        header("X-Onyx-From-Cache: 1");
    }

    /**
     * getPublicHeaders
     * store in cache only public headers, definitely not Set-Cookie header!!!
     */
    public function getPublicHeaders()
    {
        $all_headers = headers_list();
        $public_headers = [];

        foreach ($all_headers as $item) {
            if (
                preg_match('/^Content-Type/i', $item) ||
                preg_match('/^Access-Control-Allow-Origin/i', $item) ||
                preg_match('/^Strict-Transport-Security/i', $item) ||
                preg_match('/^X-XSS-Protection/i', $item) ||
                preg_match('/^X-Content-Type-Options/i', $item) ||
                preg_match('/^Location/i', $item)
            ) $public_headers[] = $item;
        }

        return $public_headers;
    }

    /**
     * getOutput
     */
    public function getOutput()
    {
        $result = $this->output;

        $result = $this->outputFilterGlobal($result);
        $result = $this->outputFilterPublic($result);

        return $result;
    }

    /**
     * Final output content
     */
    public function finalOutput()
    {
        $result = $this->getOutput();
        $this->closeSession();
        $this->closeDatabase();

        return $result;
    }

    /**
     * outputFilterGlobal
     */
    public function outputFilterGlobal($content)
    {
        require_once('models/common/common_uri_mapping.php');
        $Mapper = new common_uri_mapping();

        // translate /page/{ID} to URLs
        $content = $Mapper->system_uri2public_uri($content);

        // CDN rewrites for URLs
        if (ONYX_CDN && (ONYX_CDN_USE_WHEN_SSL || !isset($_SERVER['HTTPS']))) {
            require_once('lib/onyx.cdn.php');
            $CDN = new Onyx_Cdn();
            $content = $CDN->processOutputHtml($content);
        }

        // remove multiple white spaces beetween tags
        if (ONYX_COMPRESS_OUTPUT == 1) {
            $content = preg_replace("/>[\s]+</", "> <", $content);
        }

        return $content;
    }

    /**
     * Output filter for public clients (this filter should only apply when in frontend preview mode)
     */
    public function outputFilterPublic($content)
    {
        // Substitute constants in the output for logged in users
        // TODO: highlight in documentation!

        //only when not logged in backoffice
        if (!Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {
            if ($_SESSION['client']['customer']['id'] > 0) {
                $content = preg_replace("/{{customer.first_name}}/", htmlspecialchars($_SESSION['client']['customer']['first_name']), $content);
            } else {
                //assign empty string
                $content = preg_replace("/{{customer.first_name}}/", '', $content);
            }
        }

        // translations
        if (ONYX_SIMPLE_TRANSLATION_ENABLED && !ONYX_IN_BACKOFFICE) {
            $locale = $_SESSION['locale'];
            $default_locale = $GLOBALS['onyx_conf']['global']['locale'];

            if ($locale != $default_locale) {
                require_once('models/international/international_translation.php');
                $Translation = new international_translation();
                $node_id = $_SESSION['last_item'] = $_SESSION['history'][count($_SESSION['history']) - 1]['node_id'];
                $content = $Translation->translatePage($content, $locale, $node_id);
            }
        }

        return $content;
    }

    /**
     * csrfCheck
     */
    public function csrfCheck()
    {
        // generate & save csrfToken
        $CSRF_TOKEN = hash_hmac('md5', session_id(), ONYX_ENCRYPTION_SALT);
        $this->container->set('CSRF_TOKEN', $CSRF_TOKEN);

        // check if POST data are submitted
        // for testing period limited only to backoffice users
        if (Onyx_Bo_Authentication::getInstance()->isAuthenticated()) {
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
    public function isSessionRequired()
    {
        // exceptions
        $exceptions = ['/request/component/ecommerce/roundel_image'];
        if (array_key_exists('translate', $_GET) && in_array($_GET['translate'], $exceptions)) return false;

        // don't need session for cached pages
        if ($this->isPageCacheAllowed()) return false;

        return true;
    }

    /**
     * isPageCacheAllowed
     */
    public function isPageCacheAllowed()
    {
        $use_page_cache = true;

        // cache can be disabled on request
        if (isset($_GET['nocache'])) $this->disable_page_cache = $_GET['nocache'];

        // check if explicitly disabled
        if ($this->disable_page_cache || ONYX_PAGE_CACHE_TTL == 0) {
            $use_page_cache = false;
        } elseif (array_key_exists(ONYX_SESSION_NAME, $_COOKIE) || array_key_exists(ONYX_TOKEN_NAME, $_COOKIE) || $_COOKIE['identity_access_token']) {
            $use_page_cache = false;
        } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
            $use_page_cache = false;
        } else {
            // previously set (i.e. disabled) in session
            if (isset($_SESSION['use_page_cache'])) $use_page_cache = $_SESSION['use_page_cache'];

            // disable page cache for whole session after a user interaction and for backoffice users
            if ($_SERVER['REQUEST_METHOD'] === 'POST' || Onyx_Bo_Authentication::getInstance()->isAuthenticated() || $_SESSION['client']['customer']['id'] > 0) $use_page_cache = false;

            // TODO: allow to configure what _GET variables will disable page cache
            // disable page cache also when sorting and mode is submitted
            // component/ecommerce/product_list_sorting
            // or when preview_token is used, i.e. news article preview
            if (is_array($_GET['sort']) || $_GET['product_list_mode'] ||
                $_GET['preview_token'] || $_GET['preview_token'] || $_GET['nocache_session']) $use_page_cache = false;
        }

        return $use_page_cache;
    }

    /**
     * canBeSavedInCache
     */
    public function canBeSavedInCache()
    {
        return !($this->container->has('controller_error')
            || $this->container->has('omit_cache')
            || (array_key_exists('use_page_cache', $_SESSION) && $_SESSION['use_page_cache'] == false));
    }
}
