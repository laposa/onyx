<?php
/**
 * Copyright (c) 2010-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/list.php');

class Onyx_Controller_Component_News_List extends Onyx_Controller_List {

    /**
     * main action
     */

    public $Node;
     
    public function mainAction() {
    
        return $this->newsListAction();

    }
    
    /**
     * news list action
     */
     
    public function newsListAction() {
    
        /**
         * initialise
         */
         
        require_once('models/common/common_node.php');
        $this->Node = new common_node();

        $node_id = $this->GET['node_id'];
        $node_data = $this->Node->nodeDetail($node_id);
        
        /**
         * input data
         * 
         */ 

        //blog_node_id can be provided via GET parameter, find by actual content with fallback to configuration option
        if (is_numeric($this->GET['blog_node_id'])) $blog_node_id = $this->GET['blog_node_id'];
        else if (is_numeric($node_data['parent'])) $blog_node_id = $node_data['parent'];
        else $blog_node_id = $this->Node->conf['id_map-blog'];

        if (strlen(trim($this->GET['node_controller'] ?? '')) > 0) $node_controller = $this->GET['node_controller'];
        else $node_controller = 'news';

        /**
         * check
         */
         
        if (!is_numeric($blog_node_id)) {
            msg("component/news_list: blog_node_id must be numeric", 'error');
            return false;
        }
        
        /**
         * get detail of blog container node
         */
                
        $news_list_detail = $this->Node->getDetail($blog_node_id);
        $this->tpl->assign('NEWS_LIST', $news_list_detail);

        /**
         * get input variables
         */
        
        if (is_numeric($this->GET['created'] ?? null)) $created = $this->GET['created'];
        else if (preg_match('/[0-9]{4}-[0-9]{1,2}/', $this->GET['created'] ?? '')) $created = $this->GET['created'];
        else $created = '';
        
        if (is_numeric($this->GET['publish'] ?? null)) $publish = $this->GET['publish'];
        else $publish = '';
        
        if (isset($this->GET['display_pagination']) && $this->GET['display_pagination'] == 1) $display_pagination = 1;
        else $display_pagination = 0;
        
        /**
         * detect related taxonomy by provided input data via GET
         */
         
        $taxonomy_tree_id = $this->getTaxonomyList();
        
        /**
         * set IMAGE_PATH and IMAGE_RESIZE_OPTIONS
         * 
         */
         
        $this->setImageOptions();

        
        /**
         * Initialize pagination variables
         */
        
        if (is_numeric($this->GET['limit_from'])) $limit_from = $this->GET['limit_from'];
        else $limit_from = 0;
        
        if (is_numeric($this->GET['limit_per_page'])) $limit_per_page = $this->GET['limit_per_page'];
        else $limit_per_page = 10;
        
        /**
         * disable pagination when using taxonomy filter or created filter
         * if it came from HTTP GET from news_filter or archive (not internal GET)
         * can be removed when news_filter and news_archive will be improved
         */
         
        if ((is_numeric($_GET['taxonomy_tree_id'] ?? null) || is_numeric($_GET['created'] ?? null)) || ($this->GET['show_all_from_bo'] ?? false)) {
            $limit_from = 0;
            $limit_per_page = 999999;
        }
        
        $limit = "$limit_from,$limit_per_page";

        /**
         * prepare filter
         */
         
        $filter = array(
            'node_group' => 'page',
            'node_controller' => $node_controller,
            'parent' => $blog_node_id,
            'publish' => $publish,
            'created' => $created,
            'taxonomy_tree_id' => $taxonomy_tree_id
        );
        
        $news_list = $this->getNewsList($filter, $limit_from, $limit_per_page);
        
        if (is_array($news_list) && count($news_list) > 0) {
            
            /**
             * Display pagination
             */
            
            if ($display_pagination == 1) {
            
                $this->displayPagination($filter, $limit_from, $limit_per_page);
                
            }
            
            /**
             * display news list
             */
             
            $this->parseNewsList($news_list);
        }
        
        return true;
    }
    
    /**
     * getNewsListAll
     */
     
    public function getNewsListAll($filter, $sorting = 'common_node.created DESC, id DESC') {
        
        $news_list = $this->Node->getNodeList($filter, $sorting);
        
        return $news_list;
        
    }
    
    /**
     * getNewsList
     */
     
    public function getNewsList($filter, $limit_from, $limit_per_page) {
    
        /**
         * get list using filter
         */
        
        $news_list = $this->getNewsListAll($filter);
        
        /**
         * iterate items and created news_list_filtered
         * implemented pagination
         */
        
        if (is_array($news_list)) {
        
            $news_list_filtered = array();

            $odd_even = 'even'; 
            
            foreach ($news_list as $i=>$item) {
            
                //skip active article if any (e.g. for showing related articles)
                if ($this->GET['node_id'] == $item['id']) {
                    
                    //don't add to the list, but increase per page limit by 1
                    $limit_per_page++;
                    
                } else {
                
                    //check if it's within requested pagination limit
                    if ($i >= $limit_from  && $i < ($limit_from + $limit_per_page) ) {
                        
                        /**
                         * unserialize other data
                         */
                         
                        $item['other_data'] = unserialize($item['other_data']);
                        
                        /**
                         * unserialize component data
                         */
                         
                        $item['component'] = unserialize($item['component']);
                        
                        /**
                         * add author detail
                         */
                        
                        $item['author_detail'] = $this->Node->getAuthorDetailbyId($item['customer_id']);
                        
                        //overwrite author name
                        if ($item['component']['author'] != '') $item['author_detail']['name'] = $item['component']['author'];
                        
                        /**
                         * odd_even_class
                         */
                         
                        $odd_even = ( $odd_even == 'odd' ) ? 'even' : 'odd';
                        $item['odd_even_class'] = $odd_even;
                        
                        /**
                         * add disabled class if not published items are in the list
                         */
                         
                        if ($item['publish'] == 0) $item['class'] .= ' disabled';
                        
                        /**
                         * add related_taxonomy
                         */
                        
                        $item['related_taxonomy'] = $this->Node->getRelatedTaxonomy($item['id']);
                        
                        /**
                         * create taxonomy_class from related_taxonomy
                         */
                        
                        $item['taxonomy_class'] = '';
                        
                        foreach ($item['related_taxonomy'] as $t_item) {
                            $item['taxonomy_class'] .= "t{$t_item['id']} ";
                        }
                        
                        /**
                         * add teaser image
                         */
                         
                        $item['image'] = $this->Node->getTeaserImageForNodeId($item['id']);
                        
                        /**
                         * add modified item to final result
                         */
                        
                        $news_list_filtered[] = $item;
                    }
                }
            }

            return $news_list_filtered;
            
        } else {
        
            return false;
            
        }
        
    }
    
    /**
     * parseNewsList
     */
     
    public function parseNewsList($news_list) {
    
        if (!is_array($news_list)) return false;
        
        if (count($news_list) > 0) {
            
            /**
             * get comment count for all news pages
             */
             
            $comment_count = $this->Node->getCommentCount('page', 'news');
            
            /**
             * display news list
             */
             
            foreach ($news_list as $item) {
    
                /**
                 * assign node (ITEM) data
                 */
                
                $this->tpl->assign('ITEM', $item);
                
                if (is_array($item['image'])) $this->tpl->parse('content.list.item.image');
                
                /**
                 * check comments
                 */
                
                
                if ($item['component']['allow_comment'] == 1) {
                    
                    $item_comment_count = $comment_count[$item['id']];
                    if (!is_numeric($item_comment_count)) $item_comment_count = 0;
                    $this->tpl->assign('COMMENT_COUNT', $item_comment_count);
                    $this->tpl->parse('content.list.item.comment');
                    
                }
                
                /**
                 * empty author helper class
                 */
                 
                if (trim($item['component']['author']) == '') {
                    
                    $this->tpl->assign('AUTHOR_EMPTY', 'author_empty');
                    
                } else {
                    
                    $this->tpl->assign('AUTHOR_EMPTY', '');
                    
                }
                
                /**
                 * parse
                 */
                
                $this->tpl->parse('content.list.item');
            }
            
            //display news list
            $this->tpl->parse('content.list');
            
        }
        
    }
    
    /**
     * displayPagination
     */
     
    public function displayPagination($filter, $limit_from, $limit_per_page) {
    
        $full_news_list = $this->getNewsListAll($filter);
                
        $count = count($full_news_list);

        $_Onyx_Request = new Onyx_Request("component/pagination~limit_from=$limit_from:limit_per_page=$limit_per_page:count=$count~");
        $this->tpl->assign('PAGINATION', $_Onyx_Request->getContent());
                
    }
    
}
