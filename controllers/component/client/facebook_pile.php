<?php
/** 
 * Copyright (c) 2013-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once 'controllers/component/client/facebook.php';
require_once 'models/client/client_action.php';
require_once 'models/client/client_customer.php';

/**
 * Display recent frinds actions in similar manner as Facebook pile
 *
 * Parameters:
 *  num_displayed_items - number of displayed items (3 by default)
 *  action_filter - filter displayed items to specific actions (comma separated list, include 
 *                  action-object pairs, e.g. comment_on-recipe, enter-competition, etc.)
 *  fetch_stream - fetch actions directly from facebook stream instead of local client_action database
 *  
 */
class Onxshop_Controller_Component_Client_Facebook_Pile extends Onxshop_Controller_Component_Client_Facebook {

    protected $num_displayed_items = 3;
    protected $action_filter = null;
    protected $fetch_stream = false;

    /**
     * main action
     */
     
    public function mainAction() {

        $this->processInput($this->GET);

        $this->Node = new common_node(); // init model
        $this->commonAction(); // init Facebook SDK
        
        $this->ClientAction = new client_action();
        
        if ($user = $this->getUser()) {

            $friend_user_list = $this->getFriendsAppUsers();
            if (count($friend_user_list) == 0) return true; // not friends use this app

            if ($this->fetch_stream) {

                // fetch stream from Facebook
                $activity_list = $this->readFriendsStream($friend_user_list);
                $parsedItems = $this->parseFacebookActivities($activity_list);

            } else {

                // fetch actions from local database
                $friends_customer_ids = $this->facebookToCustomerIds($friend_user_list);
                $actions = $this->ClientAction->getActionsForCustomers($friends_customer_ids, $this->num_displayed_items * 2, $this->action_filter);
                $parsedItems = $this->parseLocalActions($actions, $this->num_displayed_items);

            }

            if ($parsedItems < $this->num_displayed_items) $parsedItems += $this->parseFriendList($friend_user_list, $this->num_displayed_items - $parsedItems);
        }

        if ($parsedItems > 0) $this->tpl->parse('content.title');

        return true;

    }

    /**
     * Parse facebook activities
     */
    protected function parseFacebookActivities($activity_list)
    {
        $parsedItems = 0;

        if (is_array($activity_list)) {
        
            foreach ($activity_list as $item) {
            
                $post_detail = $this->makeApiCallCached("/{$item['post_id']}");

                if (preg_match('/[0-9]+$/', $post_detail['link'], $matches)) {
                    
                    $node_id = $matches[0];
                    $node_detail = $this->Node->getDetail($node_id);
                    
                    //cut off long titles
                    if (strlen($node_detail['title']) > 32) $node_detail['title'] = substr($node_detail['title'], 0, 32) . '…' ;
                    
                    $this->tpl->assign('NODE', $node_detail);
                    $this->tpl->assign('FACEBOOK_POST', $post_detail);
                    $this->tpl->assign('ACTION', "recommended");
                    $this->tpl->parse('content.item_activity');
                    $parsedItems++;
                }

                if ($parsedItems == $num_displayed_items) return $parsedItems;                  
                
            }
        }

        return $parsedItems;

    }

    /**
     * Parse friendlist
     */
    protected function parseFriendList($friend_user_list, $num_displayed_items)
    {
        $parsedItems = 0;

        if (is_array($friend_user_list)) {
        
            foreach ($friend_user_list as $item) {
                
                $user_detail = $this->makeApiCallCached("/{$item}");
                
                if ($user_detail) {

                    $this->tpl->assign('FACEBOOK_USER', $user_detail);
                    $this->tpl->parse('content.item_friend_user');
                    
                    $parsedItems++;
                }

                if ($parsedItems == $num_displayed_items) return $parsedItems;                  
            }
        }

        return $parsedItems;
    }

    /**
     * Parse local actions
     */
    protected function parseLocalActions($actions, $num_displayed_items)
    {
        $parsedItems = 0;

        foreach ($actions as $action) {

            $post_detail = $this->makeApiCallCached("/{$action['action_id']}");

            if ($post_detail) {

                $node_detail = $this->Node->getDetail($action['node_id']);
                
                //cut off long titles
                if (strlen($node_detail['title']) > 32) $node_detail['title'] = substr($node_detail['title'], 0, 32) . '…' ;
                
                $this->tpl->assign('NODE', $node_detail);
                $this->tpl->assign('FACEBOOK_POST', $post_detail);
                $this->tpl->assign('ACTION', $this->getPastTense($action['action_name']));
                $this->tpl->parse('content.item_activity');
                $parsedItems++;

            }

            if ($parsedItems == $num_displayed_items) return $parsedItems;
        }

        return $parsedItems;

    }

    /**
     * Process input parameters
     * @param  array $input Array (GET)
     */
    protected function processInput($input)
    {
        // numberg of displayed items (3 by default)
        if (is_numeric($input['num_displayed_items'])) $this->num_displayed_items = $input['num_displayed_items'];

        // allow to display only specific actions (comma separated list)
        if (strlen($input['action_filter']) > 0) $this->action_filter = explode(",", $input['action_filter']);

        // allow to display stream activities fetched directly from Facebook
        if ($input['fetch_stream'] == 1) $this->fetch_stream = true;

    }   

    /**
     * Get action past tense (enter => entered, etc.)
     * @param  String $action Action verb present tense
     * @return String
     */
    protected function getPastTense($action)
    {
        switch ($action) {
            case 'comment_on': return 'commented on';
            case 'enter': return 'entered';
            case 'vote_in': return 'voted in';
            case 'like': return 'likes';
        }
        return 'recommended';
    }

    /**
     * Read friends stream from Facebook
     */
     
    protected function readFriendsStream($friend_user_list) {
    
        $friends = $this->ClientAction->prepareListForSql($friend_user_list, true);

        if (!$friends) return false;

        $fql = "SELECT app_id, type, created_time, post_id, actor_id, message, action_links, 
            description, permalink
            FROM stream
            WHERE filter_key IN (
                SELECT filter_key 
                FROM stream_filter 
                WHERE uid = me()
            ) AND actor_id IN ($friends)
            AND app_id = " . ONXSHOP_FACEBOOK_APP_ID . " LIMIT 3";
    
        $response = $this->makeApiCallCached(array(
                'method' => 'fql.query',
                'query' =>$fql
            ));
        
        return $response;
    }
    
    /**
     * Get list of friends who use the app
     */
     
    protected function getFriendsAppUsers() {
        
        $response = $this->makeApiCallCached(array('method' => 'friends.getAppUsers'));
        
        return $response;
    }

    /**
     * Convert facebook user ids to local customer ids
     * 
     * @return array
     */
    protected function facebookToCustomerIds($facebook_ids)
    {
        $result = array();

        if (is_array($facebook_ids) && count($facebook_ids) > 0 && $ids = $this->ClientAction->prepareListForSql($facebook_ids, true)) {

            $Customer = new client_customer();
            $list = $Customer->listing("facebook_id IN ($ids)");
            if (is_array($list) && count($list) > 0) foreach ($list as $item) $result[] = $item['id'];
        }

        return $result;
    }

}
