<?php
/** 
 * Copyright (c) 2010-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/common/common_node.php');
require_once('models/common/common_image.php');
require_once('models/common/common_comment.php');

class Onyx_Controller_Bo_Import_Wordpress extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        // echo "imported"; return false;
        
        define('BLOG_NODE_ID_FOR_IMPORT', 83);
        define('WORDPRESS_FILES_DOMAIN_NAME', 'www.example.com');
        define('WORDPRESS_CONTENT_PATH', '/wp-content/uploads/');
                
        $import_file = ONYX_PROJECT_DIR . '/example.2014-03-28.xml';
        
        if (!file_exists($import_file)) {
            msg("Wordpress file $import_file doesn't exists", 'error');
            return false;
        }

        $data_to_import = $this->parseWxr($import_file);
        
        $data_to_import = $this->prepareForImport($data_to_import);
        //print_r($data_to_import);
        $data_to_import = $this->insertToOnyx($data_to_import);
        
        //output
        $result = print_r($data_to_import, true);
        $this->tpl->assign('RESULT', $result);
        
        return true;
        
    }

    /**
     * parse Wordpress eXtented RSS (WXR)
     */
     
    public function parseWxr($import_file) {
        
        $parsed_data = array();
        
        $xml = simplexml_load_file($import_file, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        foreach ($xml->channel->item as $item) {
            
            $excerpt = $item->children('http://wordpress.org/export/1.2/excerpt/');
            $content = $item->children('http://purl.org/rss/1.0/modules/content/');
            $dc = $item->children('http://purl.org/dc/elements/1.1/'); 
            $wp = $item->children('http://wordpress.org/export/1.2/');
            
            $article = array();
            $article['title'] = (string) $item->title;
            $article['post_id'] = (string) $wp->post_id;
            $article['excerpt'] = (string) $excerpt->encoded;
            $article['content'] = (string) $content->encoded;
            $article['post_parent'] = (string) $wp->post_parent;
            $article['post_date'] = (string) $wp->post_date;
            $article['status'] = (string) $wp->status;
            $article['post_type'] = (string) $wp->post_type;
            
            if (count($wp->attachment_url) > 0) {           
                foreach ($wp->attachment_url as $attachment_url) {
                    $article['attachment_url'][] = (string) $attachment_url;
                }
            }
            
            $article['category'] = (string) $item->category;
            
            if (count($wp->comment) > 0) {
                foreach ($wp->comment as $comment) {
                    $article['comment'][] = (array) $comment;
                }
            }
            
            $parsed_data[]= $article;
        }
        
        return $parsed_data;
    }
    
    /**
     * prepare for import
     */
     
    public function prepareForImport($parsed_wxr) {
        
        foreach ($parsed_wxr as $item) {
            if ($item['status'] == 'publish' || $item['status'] == 'inherit')
            //post, attachment, safecss
            $formated[$item['post_type']][$item['post_id']] = $item;
        }
        
        foreach ($formated['attachment'] as $key=>$item) {
            //post_parent = 0 are global files (attachement), we don't need them
            if ($item['post_parent'] > 0) $formated['post'][$item['post_parent']]['attachment'][] = $item;  
        }
        
        $result = $formated['post'];
        
        return $result;
    }
    
    
    /**
     * prepareForOnyx
     */
     
    public function insertToOnyx($items) {
        
        $formated = array();
        
        foreach ($items as $key=>$item) {
        
            $common_node = $this->formatPostForOnyx($item);
            
            if ($node_id = $this->insertNode($common_node)) {
                
                if (is_array($item['attachment'])) {
                
                    /**
                     * download
                     */
                    
                    foreach ($item['attachment'] as $image) {
                    
                        if ($downloaded_image = $this->downloadFile($image['attachment_url'][0], ONYX_PROJECT_DIR . 'var/files/blog/')) msg("Downloaded {$image['attachment_url'][0]} to $downloaded_image");
                    }
                    
                    /**
                     * format for Onyx
                     */
                     
                    $images = $this->formatAttachmentForOnyx($item['attachment'], $node_id);
                    
                    foreach ($images as $common_image) {
                        //insert
                        $this->insertImage($common_image);
                    }
                }
                
                if (is_array($item['comment'])) {
                    
                    $comments = $this->formatCommentForOnyx($item['comment'], $node_id);
                    
                    foreach ($comments as $common_comment) {
                        $this->insertComment($common_comment);
                    }
                }
            } else {
                msg("Cannot insert WP post id {$item['post_id']} to Onyx", 'error');
            }
        }
        
        return $formated;
    }
    
    /**
     * formatPostForOnyx
     */
     
    public function formatPostForOnyx($item) {
        
        $formated = array();
        
        $formated['title'] = $item['title'];
        $formated['description'] = $item['excerpt'];
        $formated['keywords'] = $item['category'];
        $formated['created'] = $item['post_date'];
        $formated['content'] = preg_replace("/http:\/\/" . WORDPRESS_FILES_DOMAIN_NAME . addcslashes(WORDPRESS_CONTENT_PATH, '/') . "[0-9]{4}\/[0-9]{2}\//", "/image/var/files/blog/", $item['content']);
        $formated['node_group'] = 'page';
        $formated['node_controller'] = 'news';
        $formated['parent'] = BLOG_NODE_ID_FOR_IMPORT;
        $formated['parent_container'] = 0;
        $formated['modified'] = date('c');
        $formated['publish'] = 1;
        $formated['priority'] = 1;
        $formated['display_in_menu'] = 1;
        $formated['display_permission'] = 0;
        $formated['css_class'] = '';
        $formated['display_breadcrumb'] = 0;
        $formated['browser_title'] = '';
        $formated['link_to_node_id'] = 0;
        $formated['require_ssl'] = 0;
        $formated['author'] = 999;
        $formated['layout_style'] = 'fibonacci-5-3';
        $formated['component'] = 'a:2:{s:6:"author";s:0:"";s:13:"allow_comment";i:0;}';
        
        return $formated;
        
    }
    
    /**
     * formatAttachmentForOnyx
     */
     
    public function formatAttachmentForOnyx($attachments, $node_id) {
        
        $formated = array();
        $formated_item = array();
        
        foreach ($attachments as $item) {
        
            $formated_item['src'] = (string) $item['attachment_url'][0];
            $formated_item['src'] = preg_replace("/http:\/\/" . WORDPRESS_FILES_DOMAIN_NAME . addcslashes(WORDPRESS_CONTENT_PATH, '/') . "[0-9]{4}\/[0-9]{2}\//", "var/files/blog/", $formated_item['src']);
            $formated_item['role'] = 'main';
            $formated_item['node_id'] = $node_id;
            $formated_item['title'] = (string) $item['title'];
            $formated_item['description'] = (string) $item['excerpt'];
            $formated_item['modified'] = (string) $item['post_date'];
            $formated_item['author'] = 999;
            $formated_item['priority'] = 0;
            
            $formated[] = $formated_item;
        }
        
        return $formated;
        
    }
    
    /**
     * formatCommentForOnyx
     */
     
    public function formatCommentForOnyx($comments, $node_id) {
        
        $formated = array();
        $formated_item = array();
        
        foreach ($comments as $item) {
        
            $formated_item['node_id'] = $node_id;
            $formated_item['title'] = 'Comment';
            $formated_item['content'] = (string) $item['comment_content'];
            $formated_item['author_name'] = (string) $item['comment_author'];
            $formated_item['author_email'] = (string) $item['comment_author_email'];
            if (trim($formated_item['author_email']) == '') $formated_item['author_email'] = 'anonym@example.com';
            $formated_item['author_website'] = (string) $item['comment_author_url'];
            $formated_item['author_ip_address'] = (string) $item['comment_author_IP'];
            $formated_item['created'] = (string) $item['comment_date'];
            $formated_item['publish'] = $item['comment_approved'];
            $formated_item['customer_id'] = 0;
            
            $formated[] = $formated_item;
        }
        
        return $formated;
        
    }


    /**
     * insert node
     */
     
    public function insertNode($data) {
    
        $Node = new common_node();
        
        //print_r($data);
        //return 1;
        
        if ($id = $Node->insert($data)) {
            return $id;
        } else {
            msg("Cannot insert node " . print_r($data, true), 'error');
            return false;
        }
    }
    
    /**
     * insert node
     */
     
    public function insertImage($data) {
    
        $Image = new common_image();
        
        //print_r($data);
        //return 1;
        
        /** 
         * insert
         */
         
        if ($id = $Image->insert($data)) {
            return $id;
        } else {
            msg("Cannot insert image " . print_r($data, true), 'error');
            return false;
        }
    }
    
    /**
     * insert node
     */
     
    public function insertComment($data) {
        
        $Comment = new common_comment();
        
        //print_r($data);
        //return 1;
        
        if ($id = $Comment->insert($data)) {
            return $id;
        } else {
            msg("Cannot insert comment " . print_r($data, true), 'error');
            return false;
        }
        
    }
    
    /**
     * downloadFile (download URL to var/files/)
     */
    
    function downloadFile($url, $local_path = false, $new_filename = false) {
        
        if ($local_path == false) $local_path = ONYX_PROJECT_DIR . 'var/files/';
        
        $url_info = parse_url($url);
        $file_info = pathinfo($url_info['path']);
        $original_filename = $file_info['basename'];
        
        if ($new_filename) $download_filename = $new_filename;
        else $download_filename = $original_filename;
        
        $download_filename_full_path = $local_path . $download_filename;
        
        set_time_limit(0);

        /**
         * local file
         */
         
        if (!file_exists($download_filename_full_path)) $fp = fopen($download_filename_full_path, 'wb');
        else {
            msg("File $download_filename_full_path already exists", 'error');
            return false;
        }
        
        if ($fp == false){
            msg("Cannot open $download_filename_full_path file for writing", 'error');
            return false;
        }
        
        /**
         * remote file
         */
        
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        curl_exec($ch);
        //msg(curl_error($ch), 'error');
        
        curl_close($ch);
        fclose($fp);
        
        return $download_filename_full_path;
    }
}


