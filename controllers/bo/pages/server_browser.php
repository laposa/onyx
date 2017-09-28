<?php
/**
 * Server filesystem browser
 *
 * Copyright (c) 2006-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Pages_Server_Browser extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {

        if ($this->GET['directory']) $base_folder = $this->GET['directory'];
        else $base_folder = 'var/files/';
        
        if ($this->GET['role']) $role = $this->GET['role'];
        else $role = 'main';
        
        //type: add_to_node, RTE
        if ($this->GET['type']) $type = $this->GET['type'];
        else $type = '';
        
        if ($this->GET['node_id']) $node_id = $this->GET['node_id'];
        else $node_id = 0;
        
        if ($this->GET['relation']) $relation = $this->GET['relation'];
        else $relation = 'node';
        
        
        $_Onxshop_Request = new Onxshop_Request("bo/component/server_browser_menu~directory=$base_folder:type=$type:role=$role:node_id=$node_id:relation=$relation:expand_all=1:type=d~");
        $this->tpl->assign("SERVER_BROWSER_TREE", $_Onxshop_Request->getContent());
        
        $_Onxshop_Request = new Onxshop_Request("bo/component/server_browser_file_list~type=$type:role=$role:node_id=$node_id:relation=$relation~");
        $this->tpl->assign("SERVER_BROWSER_FILE_LIST", $_Onxshop_Request->getContent());

        return true;
    }
}
