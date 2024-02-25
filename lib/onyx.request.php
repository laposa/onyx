<?php
/**
 * Copyright (c) 2005-2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

/**
 * Factory for creating new controller using request URI
 */
class Onyx_Request {

    var $Onyx;
    
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

    public function __toString() {
        return (string) $this->getContent();
    }
}
