<?php
/**
 * Copyright (c) 2006-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Sys_401 extends Onyx_Controller {

    /**
     * main action
     */

    public function mainAction() {

        /**
         * set 401 HTTP code
         */

        http_response_code(401);

        /**
         * don't allow to save this request to the cache
         */
        $this->container->set('omit_cahe', true);

        return true;

    }
}
