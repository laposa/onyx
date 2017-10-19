<?php
/**
 * Copyright (c) 2008-2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Tools extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        set_time_limit(0);
        
        require_once('models/common/common_file.php');
        $File = new common_file();
        
        $tool = $this->GET['tool'];
        
        switch ($tool) {
            
            case 'uri':
            
                require_once('models/common/common_uri_mapping.php');
                $CommonURIMapping = new common_uri_mapping();
                $CommonURIMapping->generateAndSaveURITable();
                msg("Nice URLs has been completely generated");
            
            break;
            
            case 'flush_thumb':
            
                if  ($File->rm(ONXSHOP_PROJECT_DIR . "var/thumbnails/*")) msg("All image thumbnails have been deleted");
                else ("Flushing thumbnails failed");
            
            break;
            
            case 'flush_tmp':
            
                if ($File->rm(ONXSHOP_PROJECT_DIR . "var/tmp/*")) msg("Temp directory has been cleaned");
                else ("Flushing temp dir failed");
            
            break;
            
            case 'flush_cache':
            
                if (onxshop_flush_cache()) msg("Cache has been refreshed");
                else msg("Flushing cache failed");
                
            break;
            
            case 'flush_api_cache':
                
                if (is_numeric($GLOBALS['onxshop_conf']['common_configuration']['api_data_version'])) $current_api_data_version = $GLOBALS['onxshop_conf']['common_configuration']['api_data_version'];
                else $current_api_data_version = 1;
                
                $api_data_version = $current_api_data_version + 1;
                
                $Configuration = new common_configuration();
                
                if ($Configuration->saveConfig('common_configuration', 'api_data_version', $api_data_version)) {
                    
                    msg("Data version of API has increased to $api_data_version");
                    
                    if (onxshop_flush_cache()) msg("Cache has been refreshed");
                    else msg("Flushing cache failed");
                    
                }
                
            break;
            
            case 'find_hard_links':
            
                require_once('models/common/common_node.php');
                $Node = new common_node();
                $hard_links = $Node->findHardLinks();
                
                foreach ($hard_links as $hard_link) {
                    $this->tpl->assign('ITEM', $hard_link);
                    $this->tpl->parse('content.hard_links.item');
                }
                $this->tpl->parse('content.hard_links');
                
            break;

            
            
            case 'find_large_sessions':
            
                require_once('models/common/common_session.php');
                require_once('models/common/common_session_archive.php');
                $Session = new common_session();
                $SessionArchive = new common_session_archive();
                
                $list_active = $Session->findLargeSessions();
                $list_archive = $SessionArchive->findLargeSessions();
                
                foreach ($list_active as $item) {
                    $item['data_size_in_kb'] = round($item['data_size_in_bytes'] / 1000, 1);
                    $this->tpl->assign('ITEM', $item);
                    $this->tpl->parse('content.large_sessions.active.item');
                }
                
                $this->tpl->parse('content.large_sessions.active');
                
                foreach ($list_archive as $item) {
                    $item['data_size_in_kb'] = round($item['data_size_in_bytes'] / 1000, 1);
                    $this->tpl->assign('ITEM', $item);
                    $this->tpl->parse('content.large_sessions.archive.item');
                }
                
                $this->tpl->parse('content.large_sessions.archive');
                
                $this->tpl->parse('content.large_sessions');
            break;

            case 'delete_orphaned_baskets':
            
                require_once('models/ecommerce/ecommerce_basket.php');
                $Basket = new ecommerce_basket();

                if ($Basket->deleteOrphanedAnonymouseBaskets()) {
                    msg('Deleted orphaned baskets older than two weeks');
                }

            break;
            
            case 'backup':
            
                $_Onxshop = new Onxshop_Request("bo/component/backup");
                $this->tpl->assign('SUB_CONTENT', $_Onxshop->getContent());
            
            break;

            default:
            
                $this->tpl->parse('content.menu');
            
            break;
        }

        return true;
    }
}
