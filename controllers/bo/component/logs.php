<?php
/**
 * Copyright (c) 2006-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Logs extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_session.php');
        require_once('models/common/common_session_archive.php');
        
        $Session = new common_session();
        $Session_archive = new common_session_archive();
        $Session->setCacheable(false);
        
        require_once('models/client/client_customer.php');
        
        // filter
        if (isset($this->GET['filter'])) $_SESSION['bo']['filter'] = $this->GET['filter'];
        
        $filter = $_SESSION['bo']['filter'];
        
        if ($filter['active'] == 1) {
            $this->tpl->assign('ACTIVE_selected_1', "selected='selected'");
        } else {
            $this->tpl->assign('ACTIVE_selected_0', "selected='selected'");
        }
        
        if (!is_numeric($filter['customer_id']) || $filter['customer_id'] < 0) $filter['customer_id'] = '';
        
        $this->tpl->assign("FILTER", $filter);
        
        $session_ttl = round($Session->conf['ttl']/3600, 1);
        
        $this->tpl->assign('SESSION_TTL', $session_ttl);
        
        $Customer = new client_customer();
        $Customer->setCacheable(false);
        
        
        //pagination
        if (is_numeric($this->GET['limit_from']) && is_numeric($this->GET['limit_per_page'])) {
            $from = $this->GET['limit_from'];
            $per_page = $this->GET['limit_per_page'];
        } else {
            $from = 0;
            $per_page = 5;  
        }
        
        $limit = "$from,$per_page";
        
        if  (is_numeric($filter['customer_id'])) {
            $where = "customer_id = {$filter['customer_id']}";
        } else {
            $where = '';
        }
        
        $count_active = $Session->count($where);
        $count_archive = $Session_archive->count($where);
        
        if ($filter['active'] == 1) {
            $sessions = $Session->listing($where, 'modified DESC', $limit);
            $count = $count_active;
        } else {
            $session_active = $Session->listing($where, 'modified DESC', $limit);
            //pagination must be handled differently
            if (count($session_active) < $per_page) {
                //start to show archive, but use different "from"
                $from_archived = $from + count($session_active) - $count_active;
                $session_archive = $Session_archive->listing($where, 'modified DESC', "$from_archived,$per_page");
                $sessions = array_merge($session_active, $session_archive);
            } else {
                $sessions = $session_active;
            }
            $count = $count_active + $count_archive;
        }
        
        foreach ($sessions as $s) {
            $s['session_data'] = $this->unserialize_session_data($s['session_data']);
            if ($s['http_referer'] == '') {
                $link_block = "referer_na";
            } else {
                $link_block = "referer_link";
            }
            
            if (!is_array($s['session_data']['history'])) $s['session_data']['history'] = array();
            
            foreach ($s['session_data']['history'] as $history) {
                //temp
                if (!is_array($history)) $history = array('time'=>'n/a', 'uri'=>$history);
                else $history['time'] = strftime('%H:%M', $history['time']);
                $this->tpl->assign('HISTORY', $history);
                $this->tpl->parse('content.item.history');
            }
        
            $s['time_diff'] = strtotime($s['modified']) - strtotime($s['created']);
            $s['time_diff'] = round($s['time_diff']/60);
            $s['created'] = strftime('%d/%m/%Y&nbsp;%H:%M', strtotime($s['created']));
            $s['modified'] = strftime('%d/%m/%Y&nbsp;%H:%M', strtotime($s['modified']));
            if ($s['customer_id'] > 0) $this->tpl->assign('CUSTOMER', $Customer->detail($s['customer_id']));
            else $this->tpl->assign('CUSTOMER', '');
            
            // show messages
            if (ONXSHOP_DEBUG_OUTPUT_FILE) {
                $messages_file = ONXSHOP_PROJECT_DIR . "var/log/messages/{$s['ip_address']}-{$s['session_id']}.log";
                if (file_exists($messages_file)) {
                    $s['messages'] = file_get_contents($messages_file);
                }
            }
            $this->tpl->assign('SESSION', $s);
            $this->tpl->parse("content.item.$link_block");
            if ($s['messages'] != '') $this->tpl->parse('content.item.session_messages');
            $this->tpl->parse('content.item');
        }
        
        //pagination
        //$link = "/backoffice/advanced/logs";
        $link = $_SERVER['REDIRECT_URL'];
        
        $_Onxshop_Request = new Onxshop_Request("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count:link=$link:option_show_all=1:option_per_page=1~");
        $this->tpl->assign('PAGINATION', $_Onxshop_Request->getContent());

        return true;
    }
    
    //this function works fine on the fasthost :)
    function unserialize_session_data( $serialized_string ) {
       $variables = array(  );
       $a = preg_split( "/(\w+)\|/", $serialized_string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
       for( $i = 0; $i < count( $a ); $i = $i+2 ) {
           $variables[$a[$i]] = unserialize( $a[$i+1] );
       }
       return( $variables );
    }
}       
