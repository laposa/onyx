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
    /** @var common_node */
    protected $node;

    public function mainAction()
    {
        require_once('models/common/common_node.php');
        $this->node = new common_node();
        $sitemap = $this->getSitemap();

        if (is_array($sitemap)) {
            foreach ($sitemap as $node) {
                $link = $this->node->getSeoURL($node['id']);
                $item['loc'] = "https://{$_SERVER['HTTP_HOST']}{$link}";
                $item['lastmod'] = date('Y-m-d', strtotime($node['modified']));
                if ($node['parent'] == $this->node->conf['id_map-global_navigation'] || $node['parent'] == $this->node->conf['id_map-primary_navigation'] || $node['parent'] == $this->node->conf['id_map-footer_navigation']) {
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

    protected function getSitemap() 
    {
        /** @var Symfony\Component\Cache\Adapter\TagAwareAdapter */
        $cache = $this->container->get('onyx_cache');

        // get the sitemap from the 24-hour cache
        $sitemap = $cache->getItem('googlesitemap');
        if ($sitemap->isHit()) {
            return $sitemap->get();
        }

        // rebuild the sitemap and save it to cache (only if lock can be acquired)
        $lock = fopen(ONYX_SESSION_DIRECTORY . "googlesitemap.lock", 'w');
        if ($lock && flock($lock, LOCK_EX | LOCK_NB)) {
            // re-check time-limited cache in case another request rebuilt it
            $sitemap = $cache->getItem('googlesitemap');
            if (!$sitemap->isHit()) {
                $content = $this->node->getFlatSitemap();
                $sitemap->set($content);
                $sitemap->expiresAfter(86400); // 24 hours
                $cache->save($sitemap);

                $sitemapLongLived = $cache->getItem('googlesitemap_longlived');
                $sitemapLongLived->expiresAfter(31536000); // 1 year
                $sitemapLongLived->set($content);
                $cache->save($sitemapLongLived);
            }

            flock($lock, LOCK_UN);
        }
        if ($lock) fclose($lock);

        // fallback to the long-lived cache if lock cannot be acquired
        $sitemap = $cache->getItem('googlesitemap_longlived');
        if ($sitemap->isHit()) {
            return $sitemap->get();
        }

        // if all else fails, build the sitemap without caching
        return $this->node->getFlatSitemap();
    }
}
