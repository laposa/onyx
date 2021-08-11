<?php
/**
 * Copyright (c) 2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

interface Onyx_Controller_Base {

    public function __construct($request = false, &$subOnyx = false);
    public function process($request, &$subOnyx = false);
    public function mainAction();

}
