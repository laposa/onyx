<?php
/**
 * SEO manager
 *
 * Copyright (c) 2010-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


class Onxshop_Controller_Bo_Component_Seo_Manager extends Onxshop_Controller {  
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        require_once('models/common/common_node.php');
        $Node = new common_node();

        require_once('models/common/common_uri_mapping.php');
        $Mapping = new common_uri_mapping();
        
        $uri_list = $Mapping->getDetailList();
        //print_r($uri_list);
        foreach ($uri_list as $item) {
            
            if ($item['type'] == '301') {
                $item['title'] = '';
                $item['strapline'] = '';
                $item['description'] = '';
                $item['keywords'] = '';
            }
        
            if ($item['page_title'] == '') $item['page_title'] = $item['title'];
            
            $this->tpl->assign('ITEM', $item);
            
            //temporarily disable 301 management - don't display 301 in the list
            //and don't display 404 page
            //and not symbolic node_controller
            if ($item['type'] != '301' && $item['id'] != 14 && $item['node_controller'] != 'symbolic') $this->tpl->parse('content.item');
        }
            
        return true;
        
    }
    
}
