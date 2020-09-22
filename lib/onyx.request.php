<?php
/**
 * Copyright (c) 2005-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

/**
 * Factory for creating new controller using request URI
 */
class Onyx_Request {

    /**
     * Construct
     */

    public function __construct($request, &$subOnyx = false)
    {
        $this->Onyx = Onyx_Controller::createController($request, $subOnyx);
    }

    public function getContent()
    {
        return $this->Onyx->getContent();
    }
}

/**
 * compatibility nSite class
 */
 
class nSite extends Onyx_Request {
    
}
