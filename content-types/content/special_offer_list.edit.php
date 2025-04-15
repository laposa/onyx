<?php
/**
 * Copyright (c) 2013-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');
require_once('models/common/common_taxonomy.php');
require_once('models/ecommerce/ecommerce_offer.php');
require_once('models/ecommerce/ecommerce_offer_group.php');
require_once('models/ecommerce/ecommerce_product_image.php');

class Onyx_Controller_Bo_Node_Content_Special_Offer_List extends Onyx_Controller_Bo_Node_Content_Default {
    
    /**
     * post action
     */
     
    function post() {
    
        parent::post();
        
        //dropdowns
        $Offer_Group = new ecommerce_offer_group();
        $Taxonomy = new common_taxonomy();
        $conf = ecommerce_offer::initConfiguration();

        $groups_in_progress = $Offer_Group->listing("schedule_start <= NOW() AND (schedule_end IS NULL OR schedule_end >= NOW())", "id DESC");
        $groups_scheduled = $Offer_Group->listing("schedule_start > NOW()", "id DESC");
        $groups_past = $Offer_Group->listing("(schedule_start < NOW() OR schedule_start IS NULL) AND schedule_end IS NOT NULL AND schedule_end < NOW()", "id DESC");
        $campaign_categories = $Taxonomy->getChildren($conf['campaign_category_parent_id']);
        $roundel_categories = $Taxonomy->getChildren($conf['roundel_category_parent_id']);

        $this->parseOffersSelectGroup($groups_in_progress, 'In Progress', $this->node_data['component']['offer_group_id']);
        $this->parseOffersSelectGroup($groups_scheduled, 'Scheduled', $this->node_data['component']['offer_group_id']);
        $this->parseOffersSelectGroup($groups_past, 'Past', $this->node_data['component']['offer_group_id']);
        $this->parseCategorySelect($campaign_categories, $this->node_data['component']['campaign_category_id'], 'campaign_category_item');
        $this->parseCategorySelect($roundel_categories, $this->node_data['component']['roundel_category_id'], 'roundel_category_item');

        $this->parseTemplatesSelect($this->node_data['component']['template']);
    }

    protected function parseTemplatesSelect($selected) {

        $templates_local = glob(ONYX_PROJECT_DIR . "templates/component/ecommerce/product_list_*.html");
        foreach ($templates_local as &$template) $template = str_replace(".html", '', str_replace("product_list_", '', basename($template)));
        $templates_global = glob(ONYX_DIR . "templates/component/ecommerce/product_list_*.html");
        foreach ($templates_global as &$template) $template = str_replace(".html", '', str_replace("product_list_", '', basename($template)));

        $templates = array_unique(array_merge($templates_global, $templates_local));
        
        sort($templates);
        
        foreach ($templates as $item) {
            
            if (!preg_match('/sorting|filter/', $item)) {
                $this->tpl->assign('ITEM', array(
                    'id' => $item,
                    'title' => $item,
                    'selected' => ($selected == $item ? 'selected="selected"' : '')
                ));
                $this->tpl->parse('content.template_item');
            }
        }
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
    
    /**
     * parseCategorySelect
     */

    protected function parseCategorySelect(&$items, $selected_id, $block_name)
    {
        foreach ($items as $item) {
            if ($item['id'] == $selected_id) $item['selected'] = 'selected="selected"';
            if (!$item['publish']) $item['label']['title'] = '* ' . $item['label']['title'];
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse("content.$block_name");
        }
    }

}
