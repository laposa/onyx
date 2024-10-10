<?php
/**
 * Backoffice product list filter
 *
 * Copyright (c) 2008-2020 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Bo_Component_Ecommerce_Product_List_Filter extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        // filter
        if (isset($_POST['product-list-filter'])) $_SESSION['bo']['product-list-filter'] = $_POST['product-list-filter'];
        
        $filter = $_SESSION['bo']['product-list-filter'] ?? [];
        $disabled = $filter['disabled'] ?? '';

        $this->tpl->assign('FILTER', $filter);
        $this->tpl->assign("DISABLED_selected_{$disabled}", "selected='selected'");

        $this->parseOffersSelect($filter['offer_group_id'] ?? false);

        /**
         * Show edit selected offer button if an offer is active
         */
        if (isset($filter['offer_group_id']) && (int) $filter['offer_group_id'] > 0) {
            $this->tpl->assign("OFFER_GROUP_ID", (int) $filter['offer_group_id']);
            $this->tpl->parse("content.edit_offer_group_button");
        }
        
        return true;
    }

    /**
     * parseOffersSelect
     */
     
    protected function parseOffersSelect($selected_id)
    {
        require_once('models/ecommerce/ecommerce_offer_group.php');
        $Group = new ecommerce_offer_group();

        $groups = $Group->listing("", "id DESC");
        $this->parseOffersSelectGroup($groups, 'List only producs in offer group', $selected_id);

        /*
        $groups = $Group->listing("schedule_start > NOW()", "id DESC");
        $this->parseOffersSelectGroup($groups, 'Future', $selected_id);

        $groups = $Group->listing("schedule_start <= NOW() AND (schedule_end IS NULL OR schedule_end >= NOW())", "id DESC");
        $this->parseOffersSelectGroup($groups, 'Current', $selected_id);

        $groups = $Group->listing("(schedule_start < NOW() OR schedule_start IS NULL) AND schedule_end IS NOT NULL AND schedule_end < NOW()", "id DESC");
        $this->parseOffersSelectGroup($groups, 'Past', $selected_id);
        */
    }

    /**
     * parseOffersSelectGroup
     */
     
    protected function parseOffersSelectGroup(&$groups, $name, $selected_id)
    {
        if (!is_array($groups) || count($groups) == 0) return;

        foreach ($groups as $group) {
            if ($group['id'] == $selected_id) $group['selected'] = 'selected="selected"';
            if (!$group['publish']) $group['title'] = '* ' . $group['title'];
            $this->tpl->assign('ITEM', $group);
            $this->tpl->parse('content.offer_group_optgroup.offer_group_item');
        }

        $this->tpl->assign('GROUP_NAME', $name);
        $this->tpl->parse('content.offer_group_optgroup');
    }

}
