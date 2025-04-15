<?php

/** 
 * Google Sitemap
 *
 * Copyright (c) 2008-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Export_Xml_Googlesitemap extends Onyx_Controller
{

    /**
     * main action
     */

    public function mainAction()
    {
        set_time_limit(0);

        require_once('models/common/common_node.php');
        $Node = new common_node();
        $sitemap = $Node->getFlatSitemap();

        if (is_array($sitemap)) {

            foreach ($sitemap as $node) {

                $link = $Node->getSeoURL($node['id']);

                $item['loc'] = "https://{$_SERVER['HTTP_HOST']}{$link}";
                $item['lastmod'] = date('Y-m-d', strtotime($node['modified']));
                if ($node['parent'] == $Node->conf['id_map-global_navigation'] || $node['parent'] == $Node->conf['id_map-primary_navigation'] || $node['parent'] == $Node->conf['id_map-footer_navigation']) {
                    $item['priority'] = 1;
                } else {
                    $item['priority'] = 0.5;
                }
                $item['id'] = $node['id'];
                $this->tpl->assign("ITEM", $item);
                $this->tpl->parse("content.item");
            }
        }

        header('Content-Type: text/xml; charset=UTF-8');
        return true;
    }
}
