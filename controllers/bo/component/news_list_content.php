<?php
/**
 * Copyright (c) 2006-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/news_list.php');

class Onyx_Controller_Bo_Component_News_List_Content extends Onyx_Controller_Component_News_List {

    /**
     * getNewsListAll
     */
     
    public function getNewsListAll($filter, $sorting = 'common_node.created DESC, id DESC') {
        
        if ($this->GET['sorting'] == 'modified') $sorting = 'common_node.modified DESC, id DESC';
        
        $news_list = $this->Node->getNodeList($filter, $sorting);
        
        foreach ($news_list as $k=>$item) {
            $relations = unserialize($item['relations']);
            $news_list[$k]['relations'] = $relations;
        }
        
        return $news_list;
        
    }
    
}
        
