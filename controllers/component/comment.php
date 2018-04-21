<?php
/** 
 * Copyright (c) 2009-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Comment extends Onxshop_Controller {

    /** 
     * main action 
     */
     
    public function mainAction() {
    
        /**
         * set variables
         */
        
        $options = array();
        $options['allow_anonymouse_submit'] = $this->GET['allow_anonymouse_submit'];
        $options['allow_anonymouse_view'] = $this->GET['allow_anonymouse_view'];
        
        if (is_array($_POST['comment'])) $data = $_POST['comment'];
        else $data = array();
        if (is_numeric($this->GET['node_id'])) $data['node_id'] = $this->GET['node_id'];
        else $data['node_id'] = 0;
        
        
        /**
         * initialize object
         */
         
        $this->Comment = $this->initializeComment();
        
        /**
         * custom action
         */
        
        
        $this->customCommentAction($data, $options);
        
        
        /**
         * destroy object
         */

        $this->Comment = false;
        
        return true;
    }
    
    /**
     * initialize comment
     */
     
    public function initializeComment() {
    
        require_once('models/common/common_comment.php');
        return new common_comment();
    }
    
    /**
     * custom comment action
     */
     
    public function customCommentAction($data, $options) {
    
        $_Onxshop_Request = new Onxshop_Request("component/comment_list~node_id={$data['node_id']}:allow_anonymouse_submit={$options['allow_anonymouse_submit']}~");
        $this->tpl->assign('COMMENT_LIST', $_Onxshop_Request->getContent());
        
        $_Onxshop_Request = new Onxshop_Request("component/comment_add~node_id={$data['node_id']}:allow_anonymouse_submit={$options['allow_anonymouse_submit']}~");
        $this->tpl->assign('COMMENT_ADD', $_Onxshop_Request->getContent());
        
    }
    
    
    /**
     * list comments
     */
     
    function listComments($node_id, $options = false) {

        $filter = array();
        $filter['node_id'] = $node_id;
        if (is_numeric($this->GET['parent'])) $filter['parent'] = $this->GET['parent'];
        else $filter['parent'] = null;
        $filter['relation_subject'] = $this->getRelationSubject();
        
        $list = $this->Comment->getCommentList($filter, 'id DESC');
        
        $published_comments_count = 0;
        
        foreach ($list as $item) {  
            
            // don't show empty records - could be used for rating only without a comment
            if (trim($item['content']) == '') continue;
            
            //display only published items, or inserted by active customer, or admin is logged in
            if ($item['publish'] == 1 || $this->checkViewPermission($item)) {
            
                /**
                 * odd_even_class
                 */
                 
                $odd_even = ( $odd_even == 'odd' ) ? 'even' : 'odd';
                $item['odd_even_class'] = $odd_even;
                    
                /**
                 * assign
                 */
                 
                $this->tpl->assign('ITEM', $item);
                
                /**
                 * check edit permission
                 */
                 
                if ($this->checkEditPermission($item)) {
                    
                    /**
                     * display status
                     */
                     
                    if ($item['publish'] == 0) $this->tpl->parse('content.comment_list.item.edit.publish_awaiting');
                    else if ($item['publish'] == 1) $this->tpl->parse('content.comment_list.item.edit.publish_approved');
                    else if ($item['publish'] == -1) $this->tpl->parse('content.comment_list.item.edit.publish_rejected');
                    
                    if ($filter['parent'] == null) $this->tpl->parse('content.comment_list.item.edit.reply');
                    
                    $this->tpl->parse('content.comment_list.item.edit');
                    
                } else {
                
                    if ($item['publish'] == 0) $this->tpl->parse('content.comment_list.item.awaiting');
                    else if ($item['publish'] == -1) $this->tpl->parse('content.comment_list.item.rejected');
                }
                
                /**
                 * rating
                 */
                 
                if ($item['rating'] > 0) {
                    $rating = round($item['rating']);
                    $_Onxshop_Request = new Onxshop_Request("component/rating_stars~rating={$rating}~");
                    $this->tpl->assign('RATING_STARS', $_Onxshop_Request->getContent());
                } else {
                    $this->tpl->assign('RATING_STARS', '');
                }
                
                /**
                 * avatar
                 */
                
                $_Onxshop_Request = new Onxshop_Request("component/client/avatar~customer_id={$item['customer_id']}~");
                $this->tpl->assign('AUTHOR_AVATAR', $_Onxshop_Request->getContent());
                
                //sub comments
                $component = 'component/comment_list';
                if (strpos($this->request, 'component/ecommerce/product_review_list') === 0) $component = 'component/ecommerce/product_review_list';
                $_Onxshop_Request = new Onxshop_Request("$component~node_id={$this->GET['node_id']}:parent={$item['id']}~");
                $this->tpl->assign("SUB_COMMENTS", $_Onxshop_Request->getContent());
                
                //parse item block
                $this->tpl->parse('content.comment_list.item');
                
                $published_comments_count++;
                
            }
        }
        
        if ($published_comments_count > 0) {
            $this->tpl->parse('content.comment_list');
        } else {
        
            if ($filter['parent'] == null) $this->tpl->parse('content.comment_list_empty');
            
        }

    }

    
    
    /**
     * get relation subject
     */
     
    public function getRelationSubject() {
                
        return '';
        
    }
    
    /**
     * checkViewPermission
     */
     
    public function checkViewPermission($item) {
        
        if ($item['customer_id'] == $_SESSION['client']['customer']['id'] && $_SESSION['client']['customer']['id'] > 0 ) return true;
        if (Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) return true;
        
        return false;
        
    }
    
    /**
     * checkEditPermission
     */
    
    public function checkEditPermission($item) {
    
        if (Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) return true;
        
        return false;
    }
    
    /**
     * checkIdentityVisibility
     */
     
    public function checkIdentityVisibility($item) {
        
        //identity input field is visible to everyone
        return true;
        
    }


    /**
     * conditional display submit form
     */
     
    public function displaySubmitForm($data, $options) {

        /**
         * display and process insert only when allowed
         */
         
        if ($_SESSION['client']['customer']['id'] || $options['allow_anonymouse_submit']) {

            if ($options['allow_anonymouse_submit'] && !$_SESSION['client']['customer']['id']) {
                $this->tpl->parse('content.comment_insert.email');
            }

            if ($_SESSION['client']['customer']['id'] > 0) {
                $data['author_email'] = $_SESSION['client']['customer']['email'];
            }
            
            if ($_POST['save']) {
            
                /**
                 * insert comment
                 */
                
                if (is_numeric($this->GET['review_id'])) {

                    $data['id'] = $this->GET['review_id'];
                    if ($this->updateComment($data, $options)) $this->tpl->parse('content.comment_updated');
                    else $this->assignAndParseForm($data);

                } else {

                    if ($this->insertComment($data, $options)) $this->tpl->parse('content.comment_inserted');
                    else $this->assignAndParseForm($data);

                }
            
            } else {
                 
                $this->assignAndParseForm($data);
            }
            
        } else {
            
            $_Onxshop_Request = new Onxshop_Request("component/client/login");
            $this->tpl->assign('LOGIN_BOX', $_Onxshop_Request->getContent());
            $this->tpl->assign('CURRENT_PAGE_ID', $_SESSION['active_pages'][0]);
            
            $this->tpl->parse('content.log_to_insert');
        }

    }

    /**
     * assign data to form and parse
     */
     
    public function assignAndParseForm($data) {
        
        /**
         * prepopulate data
         */
            
        if (is_numeric($_SESSION['client']['customer']['id']) && $_SESSION['client']['customer']['id'] > 0) {
        
            $data['customer_id'] = $_SESSION['client']['customer']['id'];
            $customer_detail = $this->Comment->getCustomerDetail($data['customer_id']);

            $data['author_name'] = "{$customer_detail['customer']['first_name']} {$customer_detail['customer']['last_name']}";
            $data['author_email'] = $customer_detail['customer']['email'];
            
        }
        
        $this->tpl->assign('COMMENT', $data);
        
        /**
         * check if identity input field is visible
         */
         
        if ($this->checkIdentityVisibility($data)) {
            $this->tpl->parse('content.comment_insert.identity_show');
        } else {
            $this->tpl->parse('content.comment_insert.identity_hidden');
        }
        
        /**
         * display insert form
         */
        
        $this->tpl->parse('content.comment_insert');
    }



    /**
     * insert comments
     */
     
    function insertComment($data, $options = false) {
        
        if ($_POST['save']) {
        
            if ($this->checkData($data, $options)) {
            
                /**
                 * set customer id
                 */
                 
                if (is_numeric($_SESSION['client']['customer']['id']) && $_SESSION['client']['customer']['id'] > 0) {
        
                    $data['customer_id'] = $_SESSION['client']['customer']['id'];
            
                } else if (!is_numeric($data['customer_id']) && $options['allow_anonymouse_submit'])  {
                    //anonymous
                    $data['customer_id'] = 0;
                }
        
                $data['relation_subject'] = $this->getRelationSubject();
                
                if (is_numeric($data['customer_id'] )) {

                    unset($data['captcha']);
                    
                    if ($this->Comment->insertComment($data)) {
                        
                        msg('Your comment has been inserted');
                        
                        return true;
                    }
                } else {
                    msg("Must be logged in!", 'error');
                    return false;
                }
                
            } else {
                
                msg("Please fill in all fields", 'error');
            }
        } else {
        
            return false;
        }
    
    }

    /**
     * update comments
     */
     
    function updateComment($data, $options = false) {
        
        if ($_POST['save']) {
        
            if ($this->checkData($data, $options)) {
            
                /**
                 * check customer id
                 */
                 
                if ($_SESSION['client']['customer']['id'] > 0 && $data['customer_id'] == $_SESSION['client']['customer']['id']) {
        
                    unset($data['captcha']);

                    $data['author_name'] = $_POST['comment']['author_name'];
                    $data['title'] = $_POST['comment']['title'];
                    $data['rating'] = $_POST['comment']['rating'];
                    $data['content'] = $_POST['comment']['content'];
                    $data['publish'] = 0;
                    
                    if ($this->Comment->updateComment($data)) {
                        
                        $this->Comment->sendNewCommentNotificationEmail($data['id'], $data);

                        msg('Your comment has been updated');
                        return true;
                    }

                } else {
                    msg("Must be logged in!", 'error');
                    return false;
                }
                
            } else {
                
                msg("Please fill in all fields", 'error');
            }
        } else {
        
            return false;
        }
    
    }

    /**
     * check data
     */
     
    public function checkData($data, $options) {
    
        if (!is_numeric($data['rating'])) return false;

        if (!$options['allow_anonymouse_submit'] && (trim($data['author_name']) == '' || trim($data['author_email']) == '')) return false;

        if ($this->enableCaptcha) {
            $node_id = (int) $this->GET['node_id'];
            $word = strtolower($_SESSION['captcha'][$node_id]);
            $isCaptchaValid = strlen($data['captcha']) > 0 && $data['captcha'] == $word;
            return $isCaptchaValid;
        }

        return true;
    }

}
