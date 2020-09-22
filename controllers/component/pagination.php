<?php
/** 
 * Copyright (c) 2006-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Pagination extends Onxshop_Controller {

    /**
     * main action
     */
    
    public function mainAction() {
 
        /**
         * count [int] - count of all items
         * from[int] - from which item we display
         * per_page[int] - items per page
         * link [string] - overwrite URL
         * passthrough_get_parameters [int] - TODO: currently 0 by default, consider making 1 by default
         * option_show_all [int] - whether to display (parse) "show all" link
         * option_per_page [int] - whether to display (parse) dropdown for changing number of items per page
        */
        
        /**
         * Compulsory input variables
         */
        
        $count = (int)$this->GET['count'];
        $from = (int)$this->GET['limit_from'];
        $per_page = (int)$this->GET['limit_per_page'];
        
        /**
         * Optional input variables
         */ 
        
        if ($this->GET['link'])  $link = $this->GET['link'];
        else $link = $_SERVER['REDIRECT_URL'];
        
        if (is_numeric($this->GET['passthrough_get_parameters'])) $passthrough_get_parameters = $this->GET['passthrough_get_parameters'];
        else $passthrough_get_parameters = 0;
        
        // display options
        if (is_numeric($this->GET['option_show_all'])) $option_show_all = $this->GET['option_show_all'];
        else $option_show_all = 1;
        if (is_numeric($this->GET['option_per_page'])) $option_per_page = $this->GET['option_per_page'];
        else $option_per_page = 0;
        
        /**
         * process only when necessary
         */
        
        if ($count > $per_page) {

            // page items       
            $pages_total = ceil($count / $per_page);
            $current_page = floor($from / $per_page) + 1;

            // setup cycle range
            $first = max(1, $current_page - round(ONXSHOP_PAGINATION_SHOW_ITEMS / 2));
            $last = $first + (ONXSHOP_PAGINATION_SHOW_ITEMS - 1);
            if ($last > $pages_total) {
                $last = $pages_total;
                $first = max(1, $last - (ONXSHOP_PAGINATION_SHOW_ITEMS - 1));
            }

            for ($i = $first; $i <= $last; $i++) {

                $limit_from = ($i - 1) * $per_page;

                if ($limit_from == 0) $limit = '';
                else $limit = "?limit_from=$limit_from&amp;limit_per_page=$per_page";

                $item = array(
                    'class' => $i == $current_page ? 'active' : '',
                    'page' => $i,
                    'link' => $link
                );
                
                if ($passthrough_get_parameters) $limit = $this->appendGetParameters($limit);
                
                $this->tpl->assign('LIMIT', $limit);
                $this->tpl->assign('ITEM', $item);
                $this->tpl->parse('content.pagination.item');
            }

            /**
             * previous
             */
             
            if ($from > 0) {
                
                $previous = max($from - $per_page, 0);
                
                if ($previous == 0) $limit = '';
                else $limit = "?limit_from={$previous}&amp;limit_per_page=$per_page";
                
                if ($passthrough_get_parameters) $limit = $this->appendGetParameters($limit);
                
                $this->tpl->assign('LIMIT', $limit);
                $this->tpl->parse('content.pagination.previous');
            }
            
            /**
             * next
             */
             
            $next = $from + $per_page;
            
            if ($next < $count)  {
                
                $limit = "?limit_from={$next}&amp;limit_per_page=$per_page";
                
                if ($passthrough_get_parameters) $limit = $this->appendGetParameters($limit);
                
                $this->tpl->assign('LIMIT', $limit);
                $this->tpl->parse('content.pagination.next');
            }
            
            /**
             * display options?
             */
             
            if ($option_show_all) {
                
                $limit = "?limit_from=0&limit_per_page=$count";
                
                if ($passthrough_get_parameters) $limit = $this->appendGetParameters($limit);
                
                $this->tpl->assign('LIMIT', $limit);
                $this->tpl->parse('content.pagination.show_all');
            }
            
            if ($option_per_page) {
                $this->tpl->assign("PAGINATION_selected_$per_page", 'selected="selected"');
                $this->tpl->parse('content.pagination.per_page');
            }
            
            // parse template
            $this->tpl->parse('content.pagination');

        }

        return true;
    }
    
    /**
     * appendGetParameters
     */
    
    public function appendGetParameters($limit = '') {
        
        if (!property_exists($this, 'params_http_query')) $this->params_http_query = $this->getParamsHttpQuery();
        
        if (!$this->params_http_query) return $limit;
        
        if ($limit == '') $limit = "?" . $this->params_http_query;
        else $limit = $limit = $limit . '&' . $this->params_http_query;

        return $limit;
        
        
    }
    
    /**
     * getParamsHttpQuery
     */
     
    private function getParamsHttpQuery() {
        
        /**
         * filter reserved keywords
         */
         
        $params_to_append = array();
        
        foreach ($_GET as $k=>$v) {
            if (!in_array($k, array('request', 'translate', 'limit_from', 'limit_per_page'))) $params_to_append[$k] = $v;
        }
        
        $params_http_query = http_build_query($params_to_append);
        
        return $params_http_query;
        
    }
}
