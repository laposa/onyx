<?php
/**
 * Copyright (c) 2009-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Component_Google_Analytics extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        if (trim($GLOBALS['onyx_conf']['global']['google_analytics']) != '') {

            if (defined('ONYX_ENABLE_AB_TESTING') && ONYX_ENABLE_AB_TESTING == true) {
                $this->tpl->assign('TEST_GROUP', $_SESSION['ab_test_group'] == 0 ? 'A': 'B');
                $this->tpl->parse('content.googleanalytics.abtesting');
            }

            $this->tpl->parse('content.googleanalytics');

        }

        return true;
    }
}
