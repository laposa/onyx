<?php
/** 
 * Copyright (c) 2007-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/image.php');

class Onyx_Controller_Component_Image_Gallery extends Onyx_Controller_Component_Image {

    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * getImageList
         */
         
        $image_list = $this->getImageList();
        
        /**
         * assignAndParse (main images)
         */
         
        $this->assignAndParse($image_list);
        
        /**
         * displayThumbnails
         */
        
        $this->displayThumbnails($image_list);

        return true;
    }
    
    /**
     * displayThumbnails
     */
    
    public function displayThumbnails($image_list) {
        
        /**
         * display thumbnails only if there is more than one item
         */
        
        $image_count = count($image_list);
        
        $this->tpl->assign('IMAGE_COUNT', $image_count);
        
        if ($image_count > 1) {
        
            $img_path = $this->getImagePath();
            
            /**
             * check requested thumbnail width
             */
             
            if (is_numeric($this->GET['thumbnail_width'])) $thumbnail_width = $this->GET['thumbnail_width'];
            else $thumbnail_width = 100;
            
            /**
             * check thumbnail constrain and set appropriate height
             */
             
            switch ($this->GET['thumbnail_constrain']) {
                
                case '1-1':
                default:
                    $thumbnail_size = "{$thumbnail_width}x{$thumbnail_width}";
                break;
                
                case '0':
                    $thumbnail_size = "{$thumbnail_width}";
                break;
            }
            
            /**
             * iterate
             */
        
            foreach ($image_list as $k=>$item) {
                
                if ($k == 0) $this->tpl->assign('FIRST_LAST', 'first');
                else if ($k == ($image_count - 1)) $this->tpl->assign('FIRST_LAST', 'last');
                else $this->tpl->assign('FIRST_LAST', '');
                
                $item['path'] = $image_list[$k]['path'] = $img_path;
                $item['thumbnail_size'] = $thumbnail_size;
                
                $this->assignAndParseThumbnailItem($item);
                
            }
        
            $this->tpl->parse('content.thumbnails');
        
        }
        
    }
    
    /**
     * assignAndParseItem
     */
    
    public function assignAndParseItem($item) {
        
        $this->tpl->assign('ITEM', $item);
                    
        if (is_numeric($item['link_to_node_id']) && $item['link_to_node_id'] > 0) $this->tpl->parse('content.item.link');
        else $this->tpl->parse('content.item.normal');
                    
        $this->tpl->parse('content.item');
            
    }
    
    /**
     * assignAndParseThumbnailItem
     */
     
    public function assignAndParseThumbnailItem($item) {
        
        $this->tpl->assign('ITEM', $item);
        $this->tpl->parse('content.thumbnails.item');
        
    }
    
}   

