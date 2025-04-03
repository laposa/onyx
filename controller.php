<?php
/**
 * Copyright (c) 2005-2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('lib/onyx.container.php');

class Onyx_Controller {

    /** request GET parameter. */
    public $request;

    /** All messages. */
    public $messages;

    /** Content after parsing. */
    public $content;

    public $modules;
    public $module;
    /** @var array */
    public $GET;
    public $_module_html;
    public $_template_dir;
    public $_module_php;
    public $http_status;

    /** @var Onyx_Container */
    protected $container;
    /** @var XTemplate */
    protected $tpl;
    protected $page_id;
    protected $parent_page_id;
    protected $title;
    protected $description;
    protected $keywords;
    protected $head;

    /**
     * Construct
     * @param bool $request
     * @param bool $subOnyx
     */
    public function __construct($request = false, &$subOnyx = false)
    {
        if ($request) return $this->process($request, $subOnyx);
        return null;
    }

    /**
     * process
     *
     * @param string $request
     * @param object|boolean $subOnyx
     * @return boolean
     */
    public function process($request, &$subOnyx = false)
    {
        if (isset($GLOBALS['components'])) {
            $GLOBALS['components'][] = [
                "time"       => microtime(true),
                "controller" => $request,
            ];
        }

        msg("ONYX_REQUEST: BEGIN $request", "ok", 2);

        // get instance of dependency container
        $this->container = Onyx_Container::getInstance();

        // save copy or GET request to local variable
        $this->GET = $_GET;

        // make current and parent page ID easily available
        if (is_array($_SESSION ?? null)) {
            if (array_key_exists('active_pages', $_SESSION)) {
                if (count($_SESSION['active_pages']) > 0) $this->page_id = $_SESSION['active_pages'][0];
                if (count($_SESSION['active_pages']) > 1) $this->parent_page_id = $_SESSION['active_pages'][1];
            }
        }

        // check request
        $this->setRequest($request);
        $module = $this->_explodeRequest($request);
        $this->_module_html = "{$module['view']}.html";
        $this->_template_dir = getTemplateDir($this->_module_html);
        $this->_module_php = ONYX_PROJECT_DIR . "controllers/{$module['controller']}.php";

        if (!file_exists($this->_module_php)) $this->_module_php = ONYX_DIR . "controllers/{$module['controller']}.php";
        $this->_initTemplate($this->_module_html);

        //look for the Onyx tags
        $this->parseContentTagsBefore();

        // main action controller
        // if some error comes from controller, save it into registry, this will not allow save cache in onyx.bootstrap
        msg("mainAction html: " . $this->_template_dir . $this->_module_html, 'ok', 2);
        msg("mainAction php: " . $this->_module_php, 'ok', 2);

        if (!$this->mainAction()) {
            $this->container->set('controller_error', $request);
            msg("Error in $request", 'error', 1);
        }

        // Sub content
        if (is_object($subOnyx)) {
            $this->tpl->assign('SUB_CONTENT', $subOnyx->getContent());
        }

        if ($this->_template_dir != '') {
            //refresh variables after processing controller
            $this->_initTemplateVariables();
            $this->_parseTemplate();
        } else {
            msg("{$this->_module_html} " . 'does not exists.', 'error', 2);
        }

        msg("ONYX_REQUEST: END $request", "ok", 2);

        return true;
    }

    /**
     * mainAction
     * @return boolean
     */
    public function mainAction()
    {
        msg("no action for {$this->request}", 'error', 2);
        return true;
    }

    /**
     * set request
     *
     * @param string $request
     */
    public function setRequest($request)
    {
        if (preg_match('/[^a-z0-9\-\._\/\&\=\{\}\$\[\]|%@]&amp;+/i', $request)) {
            die('Invalid request: ' . htmlspecialchars($request));
        } else {
            $this->request = $request;
        }
    }

    /**
     * get request
     *
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * set title
     *
     * @param string $value
     * @return boolean
     */
    public function setTitle($value)
    {
        $value = trim($value);
        if ($value == '') return false;

        $this->title = $value;
        $this->container->append('browser_title', $value, ' - ');

        return true;
    }

    /**
     * set description
     *
     * @param string $value
     * @return boolean
     */
    public function setDescription($value)
    {
        $value = trim($value);
        if ($value == '') return false;

        $this->description = $value;
        $this->container->append('description', $value, ' - ');

        return true;
    }

    /**
     * get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * set head
     *
     * @param string $head
     * @return boolean
     */
    public function setHead($value)
    {
        $value = trim($value);
        if ($value == '') return false;

        $this->head = $value;
        $value = "<!--HEAD block of {$this->_module_html} -->\n" . $value;

        if ($this->container->has('head')) {
            //because we are processing childs first, do a reverse order
            $value = $value . "\n" . $this->container->get('head');
        }

        $this->container->set('head', $value);
        return true;
    }

    /**
     * set head once
     *
     * @param string $head
     * @return boolean
     */
    public function setHeadOnce($value)
    {
        $name = 'head_' . $this->_module_html;
        if (!$this->container->has($name)) $this->setHead($value);
        $this->container->set($name, true);
        return true;
    }

    /**
     * get head
     *
     * @return string
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * set content
     *
     * @param string $content
     * @return boolean
     */
    public function setContent($content)
    {
        $this->content = $content;
        return true;
    }

    /**
     * get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * Parse Content Tags
     * @return string
     */
    public function parseContentTags()
    {
        $content = $this->tpl->filecontents;
        $contentx = array();

        if ($matches = $this->findTags($content)) {
            //contentx is used for layout mapping
            $contentx['matches'] = $matches;

            foreach ($matches[2] as $key => $xrequest) {
                preg_match_all('/GET\.([^\&~:]*)[\&]*/', $xrequest, $m);

                foreach ($m[0] as $k => $v) {
                    $xrequest = str_replace("{$v}", $this->GET[$m[1][$k]] ?? '', $xrequest);
                }

                $_xrequest = new Onyx_Request($xrequest);

                //because of stupid parseContentTagsAfter(), we have to check if it isn't already assigned
                if (!isset($this->tpl->vars["ONYX_REQUEST_{$matches[1][$key]}"]) || $this->tpl->vars["ONYX_REQUEST_{$matches[1][$key]}"] == '') {
                    $this->tpl->assign("ONYX_REQUEST_{$matches[1][$key]}", $_xrequest->getContent() ? trim($_xrequest->getContent()) : '');
                }
            }
        }

        return $contentx;
    }

    /**
     * Parse content tags before module
     */
    public function parseContentTagsBefore()
    {
        $this->parseContentTagsBeforeHook();
        $this->parseContentTags();
    }

    /**
     * hook before content tags parsed
     */
    public function parseContentTagsBeforeHook()
    {
        return true;
    }

    /**
     * find onyx request tags
     *
     * @param string $content
     * @return array|false
     */
    public function findTags($content)
    {
        preg_match_all('/\{ONYX_REQUEST_([^\}]*) #([^\}]*)\}/', $content, $matches);
        return count($matches[0]) > 0 ? $matches : false;
    }

    /**
     * find containers
     *
     * @param string $content
     * @return array
     */
    public function findContainerTags($content)
    {
        //{CONTAINER.0.content.content #RTE}
        preg_match_all('/\{CONTAINER\.([0-9]*)\.([a-zA-Z]*).[^\}]* #([^\}]*)\}/', $content, $matches);
        return count($matches[0]) > 0 ? $matches : false;
    }

    /**
     * final output
     *
     * @return string
     */
    public function finalOutput()
    {
        return $this->getContent();
    }

    /**
     * _explodeRequest
     *
     * also modifies $this->GET
     * @return array associated array of controller and view template
     */
    public function _explodeRequest($request)
    {
        // 1st method: parse (nearly) standard HTTP GET syntax
        // Add global GET parameters to $this->GET
        // variables, TODO allow variables like sort[by]
        $request = str_replace('&amp;', '&', $request);
        $request = explode('&', $request);

        for ($i = 1; $i < count($request); $i++) {
            parse_str($request[$i], $parsed_get);
            $this->GET = array_merge_recursive_distinct($this->GET, $parsed_get);
        }

        $module = $request[0];

        // view and controller
        $vc = explode('@', $module);

        if (count($vc) > 0) {
            $m['controller'] = $vc[0];
            if (isset($vc[1])) $m['view'] = $vc[1];
            else $m['view'] = $vc[0];
        } else {
            $m['controller'] = $module;
            $m['view'] = $module;
        }

        // 2nd method: parse proprietary syntax
        // It was introduces to allow passign different parameters to different controllers using the same variable name/
        // Consider deprication this feature.
        // valid syntax controller@view~param:value~
        // TODO: allow controller~param:value~@view~param:value~
        if (preg_match('/([^\~]*)\~([^\~]*)\~/i', $m['view'], $match)) {
            // variables
            parse_str(preg_replace('/:/', '&', $match[2]), $parsed_GET);
            $this->GET = array_merge($this->GET, $parsed_GET);

            // view and controller
            if (preg_match('/(.*)@([^~]*)/', $match[1], $module_override)) {
                $m['controller'] = $module_override[1];
                $m['view'] = $module_override[2];
            } else {
                $m['controller'] = $m['view'] = $match[1];
            }
        }

        return $m;
    }

    /**
     * parse template
     */
    public function _parseTemplate()
    {
        $this->_parseMessages();
        if ($title = $this->_parseTitle()) $this->setTitle($title);
        if ($description = $this->_parseDescription()) $this->setDescription($description);
        if ($keywords = $this->_parseKeywords()) $this->setKeywords($keywords);
        if ($head = $this->_parseHead()) $this->setHead($head);
        if ($head = $this->_parseHeadOnce()) $this->setHeadOnce($head);
        if ($content = $this->_parseContent()) $this->setContent($content);
    }

    /**
     * parse title
     * only if title block is present
     */
    public function _parseTitle()
    {
        if ($this->checkTemplateBlockExists('title')) {
            $this->tpl->parse('title');
            return $this->tpl->text('title');
        }

        return false;
    }

    /**
     * parse description
     * only if description block is present
     */
    public function _parseDescription()
    {
        if ($this->checkTemplateBlockExists('description')) {
            $this->tpl->parse('description');
            return $this->tpl->text('description');
        }

        return false;
    }

    /**
     * parse keywords
     * only if title block is present
     */
    public function _parseKeywords()
    {
        if ($this->checkTemplateBlockExists('keywords')) {
            $this->tpl->parse('keywords');
            return $this->tpl->text('keywords');
        }

        return false;
    }

    /**
     * parse head
     * only if head block is present
     */
    public function _parseHead()
    {
        if ($this->checkTemplateBlockExists('head')) {
            $this->tpl->parse('head');
            return $this->tpl->text('head');
        }

        return false;
    }

    /**
     * parse head once
     * only if head_once block is present
     */
    public function _parseHeadOnce()
    {
        if ($this->checkTemplateBlockExists('head_once')) {
            $this->tpl->parse('head_once');
            return $this->tpl->text('head_once');
        }

        return false;
    }


    /**
     * parse content
     * only if head block is present
     */
    public function _parseContent()
    {
        if ($this->checkTemplateBlockExists('content')) {
            $this->tpl->parse('content');
            return $this->tpl->text('content');
        }

        return false;
    }

    /**
     * parse messages
     * display and remove messages from session if message block is present
     */
    public function _parseMessages()
    {
        if (isset($_SESSION['messages']) && $_SESSION['messages'] != '') {
            if ($this->checkTemplateVariableExists('MESSAGES')) {
                $messages = '<div class="onyx-messages" role="alert">' . $_SESSION['messages'] . '</div>';

                $this->tpl->assign('MESSAGES', $messages);
                $this->tpl->parse('content.messages');

                $_SESSION['messages'] = '';
            }
        }
    }

    /**
     * check block exists in template file
     */
    public function checkTemplateBlockExists($block_name = '')
    {
        if (array_key_exists($block_name, $this->tpl->blocks)) return true;
        else return false;
    }

    /**
     * check variable exists in template file
     */
    public function checkTemplateVariableExists($variable_name)
    {
        if (preg_match('/' . $variable_name . '/', $this->tpl->filecontents)) return true;
        else return false;
    }

    /**
     * init template
     *
     * @param unknown_type $template_file
     */
    public function _initTemplate($template_file)
    {
        // core template engine
        // initialize with option to look for files in local (project) and global (onyx) directory
        $this->tpl = new XTemplate ($template_file, [ONYX_PROJECT_DIR . 'templates/', ONYX_DIR . 'templates/']);
        if (!is_object($this->tpl)) {
            $this->tpl = new stdClass();
        }
        // set base variables
        $this->_initTemplateVariables();
    }

    /**
     * Initialize global template variables
     *
     */
    public function _initTemplateVariables()
    {
        $registry = $this->_getRegistryAsArray();

        // detect SSL
        $protocol = onyxDetectProtocol();

        // detect non standard port
        $port = onyxDetectPort($protocol);

        // build URI
        $uri = "$protocol://{$_SERVER['SERVER_NAME']}$port{$_SERVER['REQUEST_URI']}";

        // assign
        $this->tpl->assign('PROTOCOL', $protocol);
        $this->tpl->assign('URI', $uri);
        $this->tpl->assign('URI_SAFE', $uri); // deprecated
        $this->tpl->assign('BASE_URI', "$protocol://{$_SERVER['SERVER_NAME']}$port");
        if (isset($_GET['request'])) $this->tpl->assign('REQUEST_URI', "$protocol://{$_SERVER['SERVER_NAME']}$port{$_SERVER['SCRIPT_NAME']}?request={$_GET['request']}");
        if (isset($GLOBALS['onyx_conf'])) $this->tpl->assign('CONFIGURATION', $GLOBALS['onyx_conf']);
        $this->tpl->assign('REGISTRY', $registry);
        $this->tpl->assign('CSRF_TOKEN', $registry['CSRF_TOKEN']);

        $this->tpl->assign('_SERVER', $_SERVER);
        if(isset($_SESSION)) $this->tpl->assign('_SESSION', $_SESSION);
        $this->tpl->assign('_POST', $_POST);
        $this->tpl->assign('_GET', $_GET);
        $this->tpl->assign('_ENV', $_ENV);

        $this->tpl->assign('GET', $this->GET);
        $this->tpl->assign('TIME', time());
        $this->tpl->assign('PAGE_ID', $this->page_id);
        $this->tpl->assign('PARENT_PAGE_ID', $this->parent_page_id);
    }

    /**
     * get registry as array
     * it's better for Xtemplate
     */
    public function _getRegistryAsArray()
    {
        return $this->container->getServices();
    }

    /**
     * Factory method for creating new controller using request URI
     * @param string $request
     * @param object $subOnyx
     * @return object
     */
    public static function createController($request, &$subOnyx = false)
    {
        $file = self::_getControllerFile($request);
        $classname = self::_getControllerClassname($file);
        $classname_local = $classname . '_Local';

        $controller_exists = false;
        if (file_exists(ONYX_PROJECT_DIR . $file)) {
            $controller_exists = true;
            require_once(ONYX_PROJECT_DIR . $file);

            if (class_exists($classname_local)) {
                return new $classname_local($request, $subOnyx);
            } elseif (class_exists($classname)) {
                return new $classname($request, $subOnyx);
            }
        } elseif (file_exists(ONYX_DIR . $file)) {
            $controller_exists = true;
            require_once(ONYX_DIR . $file);

            if (class_exists($classname)) {
                return new $classname($request, $subOnyx);
            }
        }

        if ($controller_exists) {
            // factory didn't produce any class
            $error_message = "Missing $classname or $classname_local in $request";
            echo $error_message;

            throw new ErrorException($error_message);

            return false;
        } else {
            return new $classname($request, $subOnyx);
        }
    }

    /**
     * _getControllerClassname
     * @param string $file
     * @return string $classname
     */
    private static function _getControllerClassname($file)
    {
        if (file_exists(ONYX_DIR . $file) || file_exists(ONYX_PROJECT_DIR . $file)) {
            $name = preg_replace('/^controllers\/(.*).php$/', '\1', $file);
            $classname = "Onyx_Controller_" . preg_replace("/[\/\-]/", "_", $name);
        } else {
            $classname = "Onyx_Controller";
        }
        return $classname;
    }

    /**
     * _getControllerFile
     * @param string $request
     * @return string $file
     */
    private static function _getControllerFile($request)
    {
        $filename = preg_replace("/([A-Za-z0-9_\-\/]*).*/", "\\1", $request);
        return "controllers/{$filename}.php";
    }
}
