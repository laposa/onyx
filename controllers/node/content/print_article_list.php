<?php
/** 
 * Copyright (c) 2006-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Print_Article_List extends Onxshop_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $node_id = $this->GET['id'];
        require_once('models/common/common_print_article.php');
        $PrintArticle = new common_print_article();
        
        if (is_numeric($node_id)) {
            //this return as well the file detail from filesystem
            //$files = $File->listFiles($node_id);
            $main_articles = $PrintArticle->listing("node_id=$node_id AND type = 'article'", "priority DESC, title ASC");
            $abstracts = $PrintArticle->listing("node_id=$node_id AND type = 'abstract'", "priority DESC, title ASC");
            $reviews = $PrintArticle->listing("node_id=$node_id AND type = 'review'", "priority DESC, title ASC");
        }
        foreach ($main_articles as $ma) {
            $ma['src'] = substr($ma['src'], 4);
            $ma['title'] = strtoupper($ma['title']);
            //$ma['title'] = recode_string("utf-8..flat", trim($ma['title']));
            $this->tpl->assign('ARTICLE', $ma);
            $this->tpl->parse('content.main_articles.item');
        }
        if (count($main_articles) == 0) $this->tpl->parse('content.main_articles.noitem');
        $this->tpl->parse('content.main_articles');
        
        foreach ($abstracts as $a) {
            $a['src'] = substr($a['src'], 4);
            $a['other'] = unserialize($a['other']);
            $this->tpl->assign('ARTICLE', $a);
            $this->tpl->parse('content.abstracts.item');
        }
        
        if (count($abstracts) == 0) $this->tpl->parse('content.abstracts.noitem');
        $this->tpl->parse('content.abstracts');
        
        foreach ($reviews as $r) {
            $r['src'] = substr($r['src'], 4);
            $r['other'] = unserialize($r['other']);
            $this->tpl->assign('REVIEW', $r);
            $this->tpl->parse('content.reviews.item');
        }
        
        if (count($reviews) == 0) $this->tpl->parse('content.reviews.noitem');
        $this->tpl->parse('content.reviews');
        
        $this->tpl->assign('NODE', $node_data);
        
        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }
}       
