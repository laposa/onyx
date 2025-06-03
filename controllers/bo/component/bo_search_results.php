<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Bo_Search_Results extends Onyx_Controller {

    public $Node;

    /**
     * main action
     */
     
    public function mainAction() {

        require_once('models/common/common_node.php');
        $this->Node = new common_node();

        $results = [];
        $keywords = $this->GET['search_query'] ?? '';

        if(empty($keywords)) {
            return true;
        } else {
            $this->tpl->parse('content.backdrop');
        }

        $results = $this->Node->search($keywords);

        if (!empty($results)) {
            foreach( $results as $result ) {

                $this->tpl->assign('ITEM', $result);
                $this->tpl->assign('PATH', $this->buildPath($result['id']));
                $this->tpl->parse('content.results.item');
            }

            $this->tpl->parse('content.results');
        }

        if (!empty($keywords) && empty($results)) {
            $this->tpl->parse('content.no_results');
        }

        return true;        
    }

    private function buildPath($id) {
        $path = $this->Node->getFullPathDetailForBreadcrumb($id);
        array_splice($path, -1, 1);
        $breadcrumb = '';

        foreach($path as $level) {
            if (!empty($breadcrumb)) {
                $breadcrumb .= ' > ';
            }
            $breadcrumb .= $level['title'];
        }

        return $breadcrumb;
    }
}
