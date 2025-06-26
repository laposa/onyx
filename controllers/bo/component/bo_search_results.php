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
                $this->tpl->assign('PATH', $this->buildPath($result));
                $this->tpl->parse('content.results.item');
            }

            $this->tpl->parse('content.results');
        }

        if (!empty($keywords) && empty($results)) {
            $this->tpl->parse('content.no_results');
        }

        return true;        
    }

    private function buildPath($result) {
        $ids = json_decode($result['breadcrumbs_ids'] ?? []);
        $titles = json_decode($result['breadcrumbs_titles'] ?? []);

        $breadcrumb = '';

        foreach($ids as $index => $id) {
            $title = $titles[$index] ?? '';
            if (!empty($title)) {
                $breadcrumb .= '<a href="/backoffice/content/' . $id . '">' . htmlspecialchars($title) . '</a>';
                if ($index < count($ids) - 1) {
                    $breadcrumb .= ' &raquo; ';
                }
            }
        }

        return $breadcrumb;
    }
}
