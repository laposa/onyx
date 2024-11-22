<?php
/** 
 * Copyright (c) 2013-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Deeplink_Tags extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        $Node = new common_node();
        $node_data = $Node->nodeDetail($this->GET['id']);
        
        /**
         * process app deeplink tags
         */

        $this->processDeeplink($node_data);
        
        return true;
        
    }

    /**
     * Process Deeplink for page
     */
    public function processDeeplink($node_data) {
        if ($node_data['custom_fields'] && $node_data['custom_fields']->deeplink) {

            $link_get = $_GET;
            unset($link_get['translate'], $link_get['request']);

            $link_get_str = http_build_query($link_get);

            $url = parse_url($node_data['custom_fields']->deeplink);
            $url_str = $url['scheme'] . '://' . $url['host'] . ($url['path'] ?? '');
            if(!empty($this->combineQueries($url['query'] ?? '', $link_get_str))) {
                $url_str .= '?' . $this->combineQueries($url['query'] ?? '', $link_get_str);
            }

            $this->tpl->assign('DEEPLINK_URL', $url_str);
            $this->tpl->parse('head.deeplink');
        }
    }

    function combineQueries($query1, $query2) {
        $query1 = explode('&', $query1);
        $query2 = explode('&', $query2);
        $query = array_merge($query1, $query2);
        $query_combined = [];

        foreach($query as $param) {
            $parts = explode('=', $param);
            if (count($parts) >= 2) {
                $key = $parts[0];
                $val = $parts[1];
    
                if (!array_key_exists($key, $query_combined) && !empty($val))
                    $query_combined[$key] = $val;
            }
        }
        return http_build_query($query_combined);
    }
}
