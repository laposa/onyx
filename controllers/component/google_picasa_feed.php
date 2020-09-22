<?php
/** 
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Google_Picasa_Feed extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        if ($this->GET['username'] != '') $google_picasa['username'] = $this->GET['username'];
        else {
            msg("google_picasa_feed: Please specify a username", 'error');
            return false;
        }
        
        
        // include Zend_Feed library
        require_once('Zend/Gdata/Photos.php');
        
        
        // Create an instance of the service using an unauthenticated HTTP client
        $this->service = new Zend_Gdata_Photos();
        
        
        if (is_numeric($this->GET['albumId'])) {

            $this->getAlbumDetail($google_picasa['username'], $this->GET['albumId']);
            
        } else {
        
            $this->listAlbumsByUser($google_picasa['username']);
        
        }

        return true;
    }
    
    /**
     * Retrieving A User feed
     */
    
    function listAlbumsByUser($username) {

            //require_once('Zend/Gdata/Photos/UserQuery.php');
            try {
                $userFeed = $this->service->getUserFeed($username);
            } catch (Zend_Gdata_App_Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            
            /*
            echo $userFeed->getGphotoThumbnail();
            echo $userFeed->getGphotoNickname();
            echo $userFeed->getGphotoUser();
            echo $userFeed->getTotalResults();
            */
            $albumEntry =  $userFeed->getEntry();
            
            $albums = array();
            foreach ($albumEntry as $entry) {
                //print_r(get_class_methods($entry)); exit;
                $item['location'] = $entry->getGphotoLocation()->text;
                $item['title'] = $entry->getGphotoName()->getText();
                $item['id'] = $entry->getGphotoId()->getText();
                $item['numPhotos'] = $entry->getGphotoNumPhotos()->getText();
                $item['timestamp'] = $entry->getGphotoTimestamp()->getText();
                $albums[] = $item;
            }
            
            
            foreach ($albums as $item) {
                $this->tpl->assign('ITEM', $item);
                $this->tpl->parse('content.albums.item');
            }
            $this->tpl->parse('content.albums');
    }
    
    /**
     * Retrieving An Album
     */
             
    function getAlbumDetail($username, $album_id) {
            
            require_once('Zend/Gdata/Photos/AlbumQuery.php');
            $query = new Zend_Gdata_Photos_AlbumQuery();
            $query->setUser($username);
            $query->setAlbumId($album_id);
            //maximum supported for embedding is 800px
            $query->setImgmax(640);
            
            try {
                $albumFeed = $this->service->getAlbumFeed($query);
            } catch (Zend_Gdata_App_Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        
        
            foreach ($albumFeed as $item) {
                $one = array();
                $one['title'] = $item->title->text;
                $one['id'] = $item->getGphotoId()->text;
                $mediaContentFull = $item->getMediaGroup()->getContent(); 
                $one['full'] = $mediaContentFull[0]->getUrl();
                $mediaContentThumbnail = $item->getMediaGroup()->getThumbnail();
                $one['thumbnail'] = $mediaContentThumbnail[0]->getUrl();
                $one['thumbnail_width'] = $mediaContentThumbnail[0]->getWidth();
                $one['thumbnail_height'] = $mediaContentThumbnail[0]->getHeight();
                $list[] = $one;
            }
        
            foreach ($list as $item) {
                $this->tpl->assign('ITEM', $item);
                $this->tpl->parse('content.album_detail.item');
            }
            $this->tpl->parse('content.album_detail');
    }
}
