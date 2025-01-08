<?php
/** 
 * Copyright (c) 2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Component_Bin_Empty_Confirm extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        require_once('models/common/common_node.php');
        $Node = new common_node();
        $id_map = $Node->getIdMap();
        $bin_id = $Node->conf['id_map-bin'];
        $node_data = $Node->detail($bin_id);
        $node_data['other_data'] = unserialize($node_data['other_data'] ?? '');

        if (!is_numeric($bin_id)) {
            msg('id_map-bin: id is not numeric', 'error');
            return false;
        }

        //delete
        if ($this->GET['empty'] ?? false) {
        
            /**
             * create confirmation code
             */
            
            $confirmation_code = md5($bin_id . session_id()); 
            $this->tpl->assign('CONFIRMATION_CODE', $confirmation_code);
            
            /**
             * safety check we are not trying to delete some core page
             */
             
            if ($this->GET['confirm']) {
                
                //delete only if confirmation code match
                if ($this->GET['confirm'] === $confirmation_code) {

                    $bin_contents = $Node->getChildren($bin_id);
                    
                    foreach($bin_contents as $index => $garbage) {
                        msg("Almost Deleted \"{$garbage['title']}\" !", 'error');

                        if (!array_search($garbage['id'], $id_map)) {
                            if (!$Node->deleteFromBin($garbage['id'] ?? false)) {
                                msg("Can't delete \"{$garbage['title']}\" !", 'error');
                            }
                        } else {
                            msg("This can't be deleted", 'error');
                        }
                    }

                    $node_data['other_data']['last_empty'] = date('c');
                    $Node->nodeUpdate($node_data);

                    msg("Bin has been succesfully emptied", 'error');
                    return true;
                    
                } else {
                    
                    msg("node_delete: incorrect confirmation code", 'error');
                
                }
                                    
            } else {

                $bin_contents = $Node->getChildren($bin_id);
                $this->tpl->assign("COUNT", count($bin_contents ?? []));
                $this->tpl->parse('content.confirm');
            
            }
        }

        return true;
    }
    
}
