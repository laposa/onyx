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
require_once('models/ecommerce/ecommerce_price.php');

class Onyx_Controller_Bo_Component_Ecommerce_Product_Special_Offers extends Onyx_Controller {

    public $Offer;
    public $Offer_Group;
    public $Taxonomy;
    public $Price;
    public $conf;
    public $product_variety_id;
    public $offers;
    public $groups_in_progress;
    public $groups_scheduled;
    public $groups_past;
    public $campaign_categories;
    public $roundel_categories;
    public $prices;
    
    /**
     * main action
     */
     
    public function mainAction()
    {
        $this->initModels();
        $this->product_variety_id = (int) $this->GET['id'];
        if (isset($_POST['save']) && $_POST['save'] == 'save') $this->saveData($_POST['product']);
        $this->loadData();
        $this->parseOffers();

        return true;
    }

    protected function initModels()
    {
        $this->Offer = new ecommerce_offer();
        $this->Offer_Group = new ecommerce_offer_group();
        $this->Taxonomy = new common_taxonomy();
        $this->Price = new ecommerce_price();

        $this->conf = ecommerce_offer::initConfiguration();
    }

    protected function loadData()
    {
        $this->offers = $this->Offer->listing("product_variety_id = {$this->product_variety_id}", "id DESC");
        $this->groups_in_progress = $this->Offer_Group->listing("schedule_start <= NOW() AND (schedule_end IS NULL OR schedule_end >= NOW())", "id DESC");
        $this->groups_scheduled = $this->Offer_Group->listing("schedule_start > NOW()", "id DESC");
        $this->groups_past = $this->Offer_Group->listing("(schedule_start < NOW() OR schedule_start IS NULL) AND schedule_end IS NOT NULL AND schedule_end < NOW()", "id DESC");
        $this->campaign_categories = $this->Taxonomy->getChildren($this->conf['campaign_category_parent_id'] ?? false);
        $this->roundel_categories = $this->Taxonomy->getChildren($this->conf['roundel_category_parent_id'] ?? false);
        $this->prices = $this->Price->getPriceList($this->product_variety_id);
    }

    protected function parseOffers()
    {
        foreach ($this->offers as $offer) {

            $this->parseOffersSelectGroup($this->groups_in_progress, 'In Progress', $offer['offer_group_id']);
            $this->parseOffersSelectGroup($this->groups_scheduled, 'Scheduled', $offer['offer_group_id']);
            $this->parseOffersSelectGroup($this->groups_past, 'Past', $offer['offer_group_id']);
            $this->parseCategorySelect($this->campaign_categories, $offer['campaign_category_id'], 'campaign_category_item');
            $this->parseCategorySelect($this->roundel_categories, $offer['roundel_category_id'], 'roundel_category_item');

            $this->tpl->assign("OFFER", $offer);
            $this->tpl->parse("content.offer");
        }

        // parse empty item to be used for adding new offers
        $this->parseOffersSelectGroup($this->groups_in_progress, 'In Progress', null);
        $this->parseOffersSelectGroup($this->groups_scheduled, 'Scheduled', null);
        $this->parseOffersSelectGroup($this->groups_past, 'Past', null);
        $this->parseCategorySelect($this->campaign_categories, null, 'campaign_category_item');
        $this->parseCategorySelect($this->roundel_categories, null, 'roundel_category_item');

        $this->tpl->assign("OFFER", array('id' => '__ID__'));
        $this->tpl->parse("content.offer");

        $this->tpl->assign("VARIETY_PRICES", json_encode($this->prices));
    }

    protected function parseOffersSelectGroup(&$groups, $name, $selected_id)
    {
        if (!is_array($groups) || count($groups) == 0) return;

        foreach ($groups as $group) {
            if ($group['id'] == $selected_id) $group['selected'] = 'selected="selected"';
            if (!$group['publish']) $group['title'] = '* ' . $group['title'];
            $this->tpl->assign('ITEM', $group);
            $this->tpl->parse('content.offer.offer_group_optgroup.offer_group_item');
        }

        $this->tpl->assign('GROUP_NAME', $name);
        $this->tpl->parse('content.offer.offer_group_optgroup');
    }

    protected function parseCategorySelect(&$items, $selected_id, $block_name)
    {
        if (!is_array($items) || count($items) == 0) return;

        foreach ($items as $item) {
            if ($item['id'] == $selected_id) $item['selected'] = 'selected="selected"';
            if (!$item['publish']) $item['label']['title'] = '* ' . $item['label']['title'];
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse("content.offer.$block_name");
        }
    }

    protected function saveData($data)
    {
        if (is_array($data['offer'])) {

            foreach ($data['offer'] as $offer_id => $offer) {

                if ($offer_id == '__ID__') continue;

                if ($offer['delete'] == 1) {

                    if ($offer_id > 0) $this->Offer->delete($offer_id);

                } else {

                    $detail = array(
                        'product_variety_id' => $this->product_variety_id,
                        'offer_group_id' => $offer['offer_group_id'] > 0 ? $offer['offer_group_id'] : null,
                        'campaign_category_id' => $offer['campaign_category_id'] > 0 ? $offer['campaign_category_id'] : null,
                        'roundel_category_id' => $offer['roundel_category_id'] > 0 ? $offer['roundel_category_id'] : null,
                        'description' => $offer['description'],
                        'price_id' => $offer['price_id'] > 0 ? $offer['price_id'] : null,
                        'quantity' => $offer['quantity'] > 0 ? $offer['quantity'] : null,
                        'saving' => $offer['saving'] > 0 ? $offer['saving'] : null,
                        'modified' => date("c")
                    );

                    if ($offer_id > 0) $detail['id'] = $offer_id;
                    else $detail['created'] = date("c");

                    $this->Offer->save($detail);

                }

            }

        }
    }

}
