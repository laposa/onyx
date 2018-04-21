<?php
/** 
 * Copyright (c) 2007-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Export_Rss_Node extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * initialize
         */
         
        require_once('models/common/common_node.php');
        require_once('models/common/common_image.php');
        $Node = new common_node();
        $Image = new common_image();
        
        /**
         * find node id
         */
         
        if (is_numeric($this->GET['id'])) {
            $id = $this->GET['id'];
        } else {
            $id = $Node->conf['id_map-blog'];
        }

        /**
         * set header 
         */
         
        header('Content-Type: text/xml; charset=UTF-8');
        // flash in IE with SSL dont like Cache-Control: no-cache and Pragma: no-coche
        header("Cache-Control: ");
        header("Pragma: ");

        /**
         * Initialize pagination variables
         */
        
        if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
        else $from = 0;
        if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
        else $per_page = 25;
        
        $limit = "$from,$per_page";
        
        /**
         * latest date
         */
         
        $rss_date = date('r', time());
        $this->tpl->assign("RSS_DATE", $rss_date);
        
        /**
         * check
         */
         
        if (!is_numeric($id)) {
            msg('export rss: id is not numeric', 'error');
            return false;
        }
        
        /**
         * process
         */
         
        $node_data = $Node->getDetail($id);
        $channel_taxonomy_labels = array();
        
        if ($node_data['publish'] == 1) {
        
            $this->tpl->assign('NODE', $node_data);
        
            $taxonomy_filter = '';
            if (is_numeric($this->GET['taxonomy_tree_id']) && $this->GET['taxonomy_tree_id'] > 0) {
                $taxonomy_filter = " AND id IN (SELECT node_id FROM common_node_taxonomy WHERE taxonomy_tree_id = {$this->GET['taxonomy_tree_id']})";
            }

            $children = $Node->listing("parent = $id AND publish = 1 AND node_group='page' $taxonomy_filter", "created DESC", $limit);
            
            foreach ($children as $c) {
                
                /**
                 * create public link
                 */
                 
                $link = $Node->getSeoURL($c['id']);
                $c['url'] = "http://{$_SERVER['HTTP_HOST']}{$link}";
                
                /**
                 * format date
                 */
                 
                $c['rss_date'] = date('r', strtotime($c['created']));
                
                /**
                 * get categories
                 */
                $taxonomy_list = $Node->getRelatedTaxonomy($c['id']);

                foreach ($taxonomy_list as $taxonomy) {
                    $this->tpl->assign('CATEGORY', $taxonomy);
                    $this->tpl->parse('content.item.category');
                    $channel_taxonomy_labels[$taxonomy['label']['title']] = true;
                }

                /**
                 * add image (not part of RSS spec)
                 */
                
                $c['image'] = $this->processImage($Image->getTeaserImageForNodeId($c['id']));

                /**
                 * assign
                 */
                 
                $this->tpl->assign('CHILD', $c);

                if ($c['image']) $this->tpl->parse("content.item.image");
                $this->tpl->parse("content.item");
            
            }
        }

        // parse channel category list
        $i = 0;
        foreach ($channel_taxonomy_labels as $label => $item) {
            if ($i + 1 < count($channel_taxonomy_labels)) $label = $label . "/";
            $this->tpl->assign('CATEGORY', $label);
            $this->tpl->parse('content.category');
            $i++;
        }

        return true;
    }

    public function processImage($image)
    {
        if (!$image) return false;

        /**
         * image size
         */
         
        if (is_numeric($this->GET['image_width']) && $this->GET['image_width'] > 0) $image_width = $this->GET['image_width'];
        else $image_width = 0;
        
        if (is_numeric($this->GET['image_height']) && $this->GET['image_height'] > 0) $image_height = $this->GET['image_height'];
        else $image_height = 0;

        $image['url'] = "http://" . $_SERVER['HTTP_HOST'];

        if ($image_width) {

            if ($image_height == 0) $image['url'] .= "/thumbnail/{$image_width}/" . $image['src'];
            else $image['url'] .= "/thumbnail/{$image_width}x{$image_height}/" . $image['src'];

            $image['width'] = $image_width;
            if ($image_height == 0) $image['height'] = round($image['imagesize']['height'] * ($image_width / $image['imagesize']['width']));
            else $image['height'] = $image_height;

        } else {

            $image['url'] .= "/image/" . $image['src'];
            $image['width'] = $image['imagesize']['width'];
            $image['height'] = $image['imagesize']['height'];

        }

        $image['type'] = str_replace("; charset=binary", "", trim($image['info']['mime-type']));

        return $image;
    }
}
