<?php
/** 
 * Copyright (c) 2013-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Special_Offer_List extends Onxshop_Controller_Node_Content_Default {

    /**
     * main action
     */

    public function mainAction() {
        
        /**
         * input
         */
         
        if ($this->GET['sort']['by']) $sort_by = $this->GET['sort']['by'];
        else $sort_by = null;
        if ($this->GET['sort']['direction']) $sort_direction = $this->GET['sort']['direction'];
        else $sort_direction = null;
        
        /**
         * initialize node
         */
         
        require_once('models/common/common_node.php');
        $Node = new common_node();

        $node_data = $Node->nodeDetail($this->GET['id']);

        /**
         * prepare query
         */
         
        $query_raw = array();

        if ($node_data['component']['template']) $query_raw['template'] = $node_data['component']['template'];
        if ($node_data['component']['offer_group_id']) $query_raw['offer_group_id'] = $node_data['component']['offer_group_id'];
        if ($node_data['component']['campaign_category_id']) $query_raw['campaign_category_id'] = $node_data['component']['campaign_category_id'];
        if ($node_data['component']['campaign_category_id']) $query_raw['campaign_category_id'] = $node_data['component']['campaign_category_id'];
        $query_raw['sort']['by'] = $sort_by;
        $query_raw['sort']['direction'] = $sort_direction;
        $query_raw['limit_per_page'] = 999;
        $query = http_build_query($query_raw, '', ':');

        /**
         * call special offer component
         */
         
        $_Onxshop_Request = new Onxshop_Request("component/ecommerce/special_offer_list~{$query}~");
        $content = $_Onxshop_Request->getContent();
        if (empty($content)) return true;
        $this->tpl->assign('PRODUCT_LIST', $content);
        $this->tpl->assign('NODE', $node_data);
        if ($node_data['display_title']) $this->tpl->parse('content.title');

        return true;
    }
}
