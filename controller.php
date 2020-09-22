<?php
/**
 * Copyright (c) 2005-2018 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller {

    /**
     * request GET parameter.
     */
    var $request;

    /**
     * All messages.
     */
    var $messages;

    /**
     * Content after parsing.
     */
    var $content;

    var $modules;

    var $module;

    var $GET;

    var $_module_html;

    var $_template_dir;

    var $_module_php;

    var $http_status;

    /**
     * Construct
     */
     
    public function __construct($request = false, &$subOnyx = false) {
        if ($request) return $this->process($request, $subOnyx);
    }
    
    /**
     * process
     *
     * @param string $request
     * @param object $subOnyx
     * @return boolean
     */
     
    public function process($request, &$subOnyx = false) {
    
        if (isset($GLOBALS['components'])) {

            $GLOBALS['components'][] = array(
                "time" => microtime(true),
                "controller" => $request
            );

            $component_index = count($GLOBALS['components']) - 1;

        }

        msg("ONYX_REQUEST: BEGIN $request", "ok", 2);
        
        /**
         * save copy or GET request to local variable
         */
         
        $this->GET = $_GET;
        
        /**
         * make current and parent page ID easily available
         */
         
        $this->page_id = $_SESSION['active_pages'][0];
        $this->parent_page_id = $_SESSION['active_pages'][1];
        
        /**
         * check request
         */
         
        $this->setRequest($request);

        $module = $this->_explodeRequest($request);
        
        $this->_module_html = "{$module['view']}.html";

        $this->_template_dir = getTemplateDir($this->_module_html);

        $this->_module_php = ONYX_PROJECT_DIR . "controllers/{$module['controller']}.php";
        if (!file_exists($this->_module_php)) $this->_module_php = ONYX_DIR . "controllers/{$module['controller']}.php";
        
        if ($this->_template_dir != '') $this->_initTemplate($this->_module_html);
    
        //look for the Onyx tags
        $this->parseContentTagsBefore();
    
        // main action controller
        // if some error comes from controller, save it into registry, this will not allow save cache in onyx.bootstrap
        
        msg("mainAction html: " . $this->_template_dir . $this->_module_html, 'ok', 2);
        msg("mainAction php: " . $this->_module_php, 'ok', 2);
        
        if (!$this->mainAction()) {
            Zend_Registry::set('controller_error', $request);
            msg( "Error in $request", 'error', 1);
        }

        /**
         * subcontent
         */
        
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

        //if all went OK, return true
        return true;
        
    }
    
    /**
     * mainAction
     * @return boolean
     */

    public function mainAction() {
    
        msg("no action for {$this->request}", 'error', 2);

        return true;
        
    }
    
    /**
     * set request
     *
     * @param string $request
     */
     
    public function setRequest($request) {

        if (preg_match('/[^a-z0-9\-\._\/\&\=\{\}\$\[\]|%@]&amp;+/i',$request)) {
            die('Invalid request: '.htmlspecialchars($request));
        } else {
            $this->request = $request;
        }
        
    }

    /**
     * get request
     *
     * @return string
     */

    function getRequest() {
    
        return $this->request;
    
    }

    /**
     * set title
     *
     * @param string $value
     * @return boolean
     */

    function setTitle($value) {
    
        $value = trim($value);
        
        if ($value != '') {
        
            $this->title = $value;
        
            if (Zend_Registry::isRegistered('browser_title')) {
                Zend_Registry::set('browser_title', Zend_Registry::get('browser_title') . ' - ' . $value);
            } else {
                Zend_Registry::set('browser_title', $value);
            }
            
            return true;
        
        } else {
        
            return false;
        
        }
        
    }
    
    /**
     * set description
     *
     * @param string $value
     * @return boolean
     */

    function setDescription($value) {
    
        $value = trim($value);
        
        if ($value != '') {
            
            $this->title = $value;
            
            if (Zend_Registry::isRegistered('description')) {
            
                Zend_Registry::set('description', Zend_Registry::get('description') . ' - ' . $value);
            
            } else {
            
                Zend_Registry::set('description', $value);
            
            }
            
            return true;
            
        } else {
            
            return false;
            
        }
        
    }
    
    /**
     * set keywords
     *
     * @param string $value
     * @return boolean
     */

    function setKeywords($value) {
    
        $value = trim($value);
        
        if ($value != '') {
        
            $this->title = $value;
        
            if (Zend_Registry::isRegistered('keywords')) {
        
                Zend_Registry::set('keywords', Zend_Registry::get('keywords') . ', ' . $value);
        
            } else {
        
                Zend_Registry::set('keywords', $value);
        
            }
            
            return true;
        
        } else {
        
            return false;
        
        }
        
    }

    /**
     * get title
     *
     * @return string
     */
 
    function getTitle() {
    
        return $this->title;
        
    }
    
    /**
     * set head
     *
     * @param string $head
     * @return boolean
     */

    function setHead($value) {
    
        $value = trim($value);
        
        if ($value != '') {
        
            $this->head = $value;
        
            $value = "<!--HEAD block of {$this->_module_html} -->\n" . $value;
        
            if (Zend_Registry::isRegistered('head')) {
        
                //because we are processing childs first, do a reverse order
                $value = $value . "\n" . Zend_Registry::get('head');
        
            }
        
            Zend_Registry::set('head', $value);
            return true;
        
        } else {
        
            return false;
        
        }
        
    }
    
    /**
     * set head once
     *
     * @param string $head
     * @return boolean
     */

    function setHeadOnce($value) {
    
        $name = 'head_' . $this->_module_html;
        
        if (!Zend_Registry::isRegistered($name)) $this->setHead($value);
        
        Zend_Registry::set($name, true);
        
        return true;    
    }

    /**
     * get head
     *
     * @return string
     */

    function getHead() {
    
        return $this->head;
        
    }

    /**
     * set content
     *
     * @param string $content
     * @return boolean
     */

    function setContent($content) {
    
        $this->content = $content;
        
        return true;
        
    }

    /**
     * get content
     *
     * @return string
     */

    function getContent() {
    
        return $this->content;
        
    }


    /**
     * Parse Content Tags
     * @return string
     */

    function parseContentTags() {
        
        $content = $this->tpl->filecontents;
        
        if ($matches = $this->findTags($content)) {
        
            //contentx is used for layout mapping
            $contentx['matches'] = $matches;
            
            foreach ($matches[2] as $key=>$xrequest) {
            
                preg_match_all('/GET\.([^\&~:]*)[\&]*/', $xrequest, $m);
                
                foreach ($m[0] as $k=>$v) {
                    $xrequest = str_replace("{$v}", $this->GET[$m[1][$k]], $xrequest);
                }
                
                $_xrequest = new Onyx_Request($xrequest);
                
                //because of stupid parseContentTagsAfter(), we have to check if it isn't already assigned 
                if ($this->tpl->vars["ONYX_REQUEST_{$matches[1][$key]}"] == '') {
                    $this->tpl->assign("ONYX_REQUEST_{$matches[1][$key]}", trim($_xrequest->getContent()));
                }
                
            }
        }
        
        return $contentx;
        
    }

    /**
     * Parse content tags before module
     */

    function parseContentTagsBefore() {
    
        $this->parseContentTagsBeforeHook();
        $this->parseContentTags();
        
    }
    
    /**
     * hook before content tags parsed
     */

    function parseContentTagsBeforeHook() {
    
        return true;
        
    }
        
    /**
     * find onyx request tags
     *
     * @param string $content
     * @return array
     */

    function findTags($content) {
    
        preg_match_all('/\{ONYX_REQUEST_([^\}]*) #([^\}]*)\}/', $content, $matches);
        
        if (count($matches[0]) > 0) {
            return $matches;
        } else {
            return false;
        }
        
    }

    /**
     * find containers
     *
     * @param string $content
     * @return array
     */

    function findContainerTags($content) {
    
        //{CONTAINER.0.content.content #RTE} 
        preg_match_all('/\{CONTAINER\.([0-9]*)\.([a-zA-Z]*).[^\}]* #([^\}]*)\}/', $content, $matches);
        
        if (count($matches[0]) > 0) {
            return $matches;
        } else {
            return false;
        }
        
    }

    /**
     * final output
     *
     * @return string
     */

    function finalOutput() {
    
        $output = $this->getContent();

        return $output;
                
    }

    /**
     * _explodeRequest
     *
     * also modifies $this->GET
     * @return array associated array of controller and view template
     */
     
    function _explodeRequest($request) {
    
        /**
         * 1st method: parse (nearly) standard HTTP GET syntax
         *
         * Add global GET parameters to $this->GET
         */
        
        // variables, TODO allow variables like sort[by]
        $request = str_replace('&amp;', '&', $request);
        $request = explode('&', $request);
        
        for ($i=1; $i<count($request); $i++) {
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

        /**
         * 2nd method: parse proprietary syntax
         *
         * It was introduces to allow passign different parameters to different controllers using the same variable name/
         * Consider deprication this feature.
         *
         * valid syntax controller@view~param:value~
         * TODO: allow controller~param:value~@view~param:value~
         */
         
        if(preg_match('/([^\~]*)\~([^\~]*)\~/i', $m['view'], $match)) {

            // variables
            parse_str(preg_replace('/:/', '&', $match[2]), $parsed_GET);
            $this->GET = array_merge($this->GET, $parsed_GET);
            
            // view and controller
            if(preg_match('/(.*)@([^~]*)/', $match[1], $module_override)) {
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
     *
     */
     
    function _parseTemplate() {
    
        $this->_parseMessages();
        if ($title  = $this->_parseTitle()) $this->setTitle($title);
        if ($description  = $this->_parseDescription()) $this->setDescription($description);
        if ($keywords  = $this->_parseKeywords()) $this->setKeywords($keywords);
        if ($head = $this->_parseHead()) $this->setHead($head);
        if ($head = $this->_parseHeadOnce()) $this->setHeadOnce($head);
        if ($content = $this->_parseContent()) $this->setContent($content);
        
    }
    
    /**
     * parse title
     * only if title block is present
     */

    function _parseTitle() {
    
        if ($this->checkTemplateBlockExists('title')) {
            
            $this->tpl->parse('title');
            return $this->tpl->text('title');
        
        } else {
        
            return false;
        
        }
        
    }
    
    /**
     * parse description
     * only if description block is present
     */

    function _parseDescription() {
    
        if ($this->checkTemplateBlockExists('description')) {
            
            $this->tpl->parse('description');
            return $this->tpl->text('description');
        
        } else {
        
            return false;
        
        }
        
    }
    
    /**
     * parse keywords
     * only if title block is present
     */

    function _parseKeywords() {
    
        if ($this->checkTemplateBlockExists('keywords')) {
            
            $this->tpl->parse('keywords');
            return $this->tpl->text('keywords');
        
        } else {
        
            return false;
        
        }
        
    }
    
    /**
     * parse head
     * only if head block is present
     */

    function _parseHead() {
    
        if ($this->checkTemplateBlockExists('head')) {
            
            $this->tpl->parse('head');
            return $this->tpl->text('head');
        
        } else {
        
            return false;
        
        }
        
    }
    
    /**
     * parse head once
     * only if head_once block is present
     */

    function _parseHeadOnce() {
    
        if ($this->checkTemplateBlockExists('head_once')) {
            
            $this->tpl->parse('head_once');
            return $this->tpl->text('head_once');
        
        } else {
        
            return false;
        
        }
        
    }
    
    
    /**
     * parse content
     * only if head block is present
     */

    function _parseContent() {
    
        if ($this->checkTemplateBlockExists('content')) {
            
            $this->tpl->parse('content');
            return $this->tpl->text('content');
        
        } else {
        
            return false;
        
        }
        
    }
    
    /**
     * parse messages
     * display and remove messages from session if message block is present
     */

    function _parseMessages() {

        if ($_SESSION['messages']) {
            
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
     
    function checkTemplateBlockExists($block_name = '') {
        
        if (array_key_exists($block_name, $this->tpl->blocks)) return true;
        else return false;
        
    }
    
    /**
     * check variable exists in template file
     */
     
    function checkTemplateVariableExists($variable_name) {
        
        if (preg_match('/'.$variable_name.'/', $this->tpl->filecontents)) return true;
        else return false;
        
    }

    /**
     * init template
     *
     * @param unknown_type $template_file
     */

     function _initTemplate($template_file) {
    
        // core template engine
        // initialize with option to look for files in local (project) and global (onyx) directory
        $this->tpl = new XTemplate ($template_file, array(ONYX_PROJECT_DIR . 'templates/', ONYX_DIR . 'templates/'));
        
        // set base variables
        $this->_initTemplateVariables();
        
    }

    /**
     * Initialize global template variables
     *
     */

    function _initTemplateVariables() {
    
        $registry = $this->_getRegistryAsArray();
        
        // detect SSL
        $protocol = onyxDetectProtocol();
        
        // detect non standard port
        if ($protocol == 'https' && ($_SERVER['SERVER_PORT'] == 443 || $_SERVER['HTTP_X_FORWARDED_PORT'] == 443)) $port = '';
        else if ($protocol == 'http' && ($_SERVER['SERVER_PORT'] == 80 || $_SERVER['HTTP_X_FORWARDED_PORT'] == 80)) $port = '';
        else if (is_numeric($_SERVER['HTTP_X_FORWARDED_PORT'])) $port = ":{$_SERVER['HTTP_X_FORWARDED_PORT']}";
        else $port = ":{$_SERVER['SERVER_PORT']}";
        
        // build URI
        $uri = "$protocol://{$_SERVER['SERVER_NAME']}$port{$_SERVER['REQUEST_URI']}";
        
        // assign
        $this->tpl->assign('PROTOCOL', $protocol);
        $this->tpl->assign('URI', $uri);
        $this->tpl->assign('URI_SAFE', $uri); // deprecated
        $this->tpl->assign('BASE_URI', "$protocol://{$_SERVER['SERVER_NAME']}$port");
        $this->tpl->assign('REQUEST_URI', "$protocol://{$_SERVER['SERVER_NAME']}$port{$_SERVER['SCRIPT_NAME']}?request={$_GET['request']}");
        $this->tpl->assign('CONFIGURATION', $GLOBALS['onyx_conf']);
        $this->tpl->assign('REGISTRY', $registry);
        $this->tpl->assign('CSRF_TOKEN', $registry['CSRF_TOKEN']);
        
        $this->tpl->assign('_SERVER', $_SERVER);
        $this->tpl->assign('_SESSION', $_SESSION);
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
     
    function _getRegistryAsArray() {
    
        $r = Zend_Registry::getInstance();
        $registry = array();
        foreach ($r as $index => $value) {
            $registry[$index] = $value;
        }
        return $registry;
        
    }


    /**
     * Factory method for creating new controller using request URI
     * @param string $request
     * @param object $subOnyx
     * @return object
     */
    
    public static function createController($request, &$subOnyx = false) {
        
        $file = self::_getControllerFile($request);
        $classname = self::_getControllerClassname($file);
        $classname_local = $classname . '_Local';
        
        if (file_exists(ONYX_PROJECT_DIR . $file)) {
            
            $controller_exists = 1;
            
            require_once(ONYX_PROJECT_DIR . $file);
            
            if (class_exists($classname_local)) {
                
                return new $classname_local($request, $subOnyx);
                
            } else if (class_exists($classname)) {
                
                return new $classname($request, $subOnyx);
                
            }
            
        } else if (file_exists(ONYX_DIR . $file)) {
            
            $controller_exists = 1;
            
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

    private static function _getControllerClassname($file) {
        
        if (file_exists(ONYX_DIR . $file) || file_exists(ONYX_PROJECT_DIR . $file)) {
            $name = preg_replace('/^controllers\/(.*).php$/', '\1', $file);
            $classname = "Onyx_Controller_" . preg_replace("/\//", "_", $name);
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

    private static function _getControllerFile($request) {

        $filename = preg_replace("/([A-Za-z0-9_\/]*).*/", "\\1", $request);
        $file = "controllers/{$filename}.php";
        
        return $file;
        
    }
    
    

}
