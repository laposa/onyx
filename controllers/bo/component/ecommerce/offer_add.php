<?php
/**
 * Backoffice product edit special offer section
 *
 * Copyright (c) 2013-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_taxonomy.php');
require_once('models/ecommerce/ecommerce_offer.php');
require_once('models/ecommerce/ecommerce_offer_group.php');
require_once('models/ecommerce/ecommerce_product.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Offer_Add extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction()
    {
        $this->initModels();
        if ($_POST['save']) $this->saveData($_POST);
        $this->loadData();
        $this->parseForm($_POST);

        return true;
    }

    protected function initModels()
    {
        $this->Offer = new ecommerce_offer();
        $this->Offer_Group = new ecommerce_offer_group();
        $this->Taxonomy = new common_taxonomy();
        $this->Product = new ecommerce_product();

        $this->conf = ecommerce_offer::initConfiguration();
    }

    protected function loadData()
    {
        $this->groups_in_progress = $this->Offer_Group->listing("schedule_start <= NOW() AND (schedule_end IS NULL OR schedule_end >= NOW())", "id DESC");
        $this->groups_scheduled = $this->Offer_Group->listing("schedule_start > NOW()", "id DESC");
        $this->groups_past = $this->Offer_Group->listing("(schedule_start < NOW() OR schedule_start IS NULL) AND schedule_end IS NOT NULL AND schedule_end < NOW()", "id DESC");
        $this->campaign_categories = $this->Taxonomy->getChildren($this->conf['campaign_category_parent_id']);
        $this->roundel_categories = $this->Taxonomy->getChildren($this->conf['roundel_category_parent_id']);
        $this->products = $this->Product->getProductListForDropdown();
    }

    protected function parseForm($offer)
    {
        $this->parseOffersSelectGroup($this->groups_in_progress, 'In Progress', $offer['offer_group_id']);
        $this->parseOffersSelectGroup($this->groups_scheduled, 'Scheduled', $offer['offer_group_id']);
        $this->parseOffersSelectGroup($this->groups_past, 'Past', $offer['offer_group_id']);
        $this->parseCategorySelect($this->campaign_categories, $offer['campaign_category_id'], 'campaign_category_item');
        $this->parseCategorySelect($this->roundel_categories, $offer['roundel_category_id'], 'roundel_category_item');
        $this->parseProductSelect();
    }

    protected function parseProductSelect()
    {
        foreach ($this->products as $product) {

            if (!$product['variety_publish'] || !$product['product_publish']) $product['class'] = 'notpublic';
            if (!$product['image_src']) $product['image_src'] = '/var/files/placeholder.png';

            $this->tpl->assign("ITEM", $product);
            $this->tpl->parse("content.product_item");
        }
    }

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

    protected function parseCategorySelect(&$items, $selected_id, $block_name)
    {
        foreach ($items as $item) {
            if ($item['id'] == $selected_id) $item['selected'] = 'selected="selected"';
            if (!$item['publish']) $item['label']['title'] = '* ' . $item['label']['title'];
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse("content.$block_name");
        }
    }

    protected function saveData($offer)
    {
        if (is_numeric($this->Offer->insertOffer($offer))) $this->ajaxMessage("Special offer successfully saved");

        if ($_SESSION['messages']) {
            $messages = $_SESSION['messages'];
            $_SESSION['messages'] = '';
            $this->ajaxMessage($messages);
        }
    }

    protected function ajaxMessage($message)
    {
        echo($message);
        exit();
    }
}
