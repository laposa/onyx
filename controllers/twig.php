<?php
/**
 * Copyright (c) 2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Twig\Extra\String\StringExtension;

class Onyx_Controller_Twig extends Onyx_Controller {

    public $variables;
    public $request;
    public $messages;
    public $content;

    public function __construct($request = false, &$subOnyx = false)
    {
        if ($request) return $this->process($request, $subOnyx);
        return null;
    }


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
        $this->page_id = $_SESSION['active_pages'][0];
        $this->parent_page_id = $_SESSION['active_pages'][1];

        // check request
        $this->setRequest($request);
        $module = $this->_explodeRequest($request);

        // Init template variables
        $this->variables = [
            'request' => $request
        ];

        // Sub content
        if (is_object($subOnyx)) $this->variables['subContent'] = $subOnyx->getContent();

        // Init engine
        $loader = new FilesystemLoader(dirname(ONYX_PROJECT_DIR . $module['view']));
        $loader->addPath(ONYX_PROJECT_DIR . 'patternlab/source/_patterns', 'patterns');
        $isDebug = (defined('ONYX_TRACY') && ONYX_TRACY === true);
        $this->twig = new Environment($loader, [
            'cache' => $isDebug ? false : ONYX_PROJECT_DIR . 'var/cache/twig',
            'autoescape' => 'html',
            'debug' => $isDebug
        ]);
        if ($isDebug) {
            $this->twig->addExtension(new DebugExtension());
        }
        $this->twig->addExtension(new StringExtension());

        // Run the controller
        if (!$this->mainAction()) {
            $this->container->set('controller_error', $request);
            msg("Error in $request", 'error', 1);
        }

        // Load template
        $this->tpl = $this->twig->load(basename($module['view']));

        //look for the Onyx tags
        $this->parseContentTagsBefore(serialize($this->tpl->getSourceContext()));

        // Render template
        $this->content = $this->tpl->render($this->variables);

        msg("ONYX_REQUEST: END $request", "ok", 2);

        return true;
    }

    public function mainAction()
    {
        msg("no action for {$this->request}", 'error', 2);
        return true;
    }

}
