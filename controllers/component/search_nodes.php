<?php

/** 
 * Copyright (c) 2006-2020 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Component_Search_Nodes extends Onyx_Controller
{

    /**
     * main action
     */

    public function mainAction()
    {

        require_once('models/common/common_node.php');
        $this->Node = new common_node();

        if (!isset($this->GET['search_query'])) {
            return true;
        }

        $searchQuery = $this->GET['search_query'];
        $count = strlen(trim($searchQuery));

        if ($count < 3) {
            msg("Please specify at least 3 characters", "error");
            return true;
        }


        $result = $this->Node->search($searchQuery);
        foreach ($result as $r) {
            $this->tpl->assign('RESULT', $r);
            $this->tpl->parse('content.result.item');
        }

        $this->tpl->parse('content.result');

        return true;
    }
}
