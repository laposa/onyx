<?php
/** 
 * Copyright (c) 2006-2020 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Search_Nodes extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        require_once('models/common/common_node.php');
        $this->Node = new common_node();
                
        if (isset($this->GET['search_query'])) {
            
            $searchQuery = $this->GET['search_query'];
            $count = strlen(trim($searchQuery));
            
            if ($count > 2) {
                
                $result = $this->Node->search($searchQuery);
            
                $added = array();
            
                foreach ($result as $r) {
                    
                    // skip bin items
                    if ($this->Node->isInBin($r['id'])) continue;
                        
                    if ($r['node_group'] != 'page') {
                        $active_pages = $this->Node->getActivePages($r['id']);
                        $r = $this->Node->detail($active_pages[0]);
                    }
                
                    if (!in_array($r['id'], $added) && $r['node_group'] == 'page' && $r['publish'] == 1) {
                        $this->tpl->assign('RESULT', $r);
                        $this->tpl->parse('content.result.item');
                        $added[] = $r['id'];
                    }
                }
            
                $this->tpl->parse('content.result');
            
            } else {
            
                msg("Please specify at least 3 characters", "error");
            
            }
        }

        return true;
    }
}
