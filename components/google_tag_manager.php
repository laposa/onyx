<?php
/**
 * Copyright (c) 2014-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Component_Google_Tag_Manager extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        if ($gtm_container_id = $this->getGTMContainerId()) {

            $this->tpl->assign('GTM_CONTAINER_ID', $gtm_container_id);
            $this->tpl->parse('head.gtm');
            $this->tpl->parse('content.gtm');

        }

        return true;
    }

    /**
     * Find container ID
     * @return mixed $gtm_container_id
     */
    public function getGTMContainerId() {

        if (getenv('ONYX_GOOGLE_TAG_MANAGER_CONTAINER_ID')) $gtm_container_id = getenv('ONYX_GOOGLE_TAG_MANAGER_CONTAINER_ID');
        else if (trim($GLOBALS['onyx_conf']['global']['google_tag_manager']) != '') $gtm_container_id = trim($GLOBALS['onyx_conf']['global']['google_tag_manager']);
        else $gtm_container_id = false;

        return $gtm_container_id;

    }
}
