<?php
/**
 * Server filesystem browser
 *
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Pages_Server_Browser extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {

        $this->tpl->assign('OPEN', $this->GET['open'] ?? 'var/files/');

        $this->tpl->assign('ROLE', $this->GET['role'] ?? 'main');
        
        //type: add_to_node, RTE
        $this->tpl->assign('TYPE', $this->GET['type'] ?? '');
        
        $this->tpl->assign('NODE_ID', $this->GET['node_id'] ?? 0);
        
        $this->tpl->assign('RELATION', $this->GET['relation'] ?? 'node');

        $this->tpl->assign('FILE_ID', $this->GET['file_id'] ?? 0);

        $this->tpl->assign('OPEN', $this->GET['open'] ?? '');

        return true;
    }
}
