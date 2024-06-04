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

        if ($this->GET['file_id']) $file_id = $this->GET['file_id'];
        else $file_id = 0;

        if ($this->GET['open']) $open = $this->GET['open'];
        else $open = null;

        if ($type === 'replace_file') $keep_url = true;
        else $keep_url = false;

        $_Onyx_Request = new Onyx_Request("bo/component/server_browser_menu~directory=$base_folder:type=$type:role=$role:node_id=$node_id:relation=$relation:file_id=$file_id:open=$open:expand_all=1:type=d~");
        $this->tpl->assign("SERVER_BROWSER_TREE", $_Onyx_Request->getContent());
        
        $_Onyx_Request = new Onyx_Request("bo/component/server_browser_file_list~type=$type:role=$role:node_id=$node_id:relation=$relation:file_id=$file_id:open=$open~");
        $this->tpl->assign("SERVER_BROWSER_FILE_LIST", $_Onyx_Request->getContent());
        $this->tpl->assign("KEEP_URL", $keep_url);

        return true;
    }
}
