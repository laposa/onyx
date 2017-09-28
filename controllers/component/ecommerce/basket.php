<?php
/** 
 * Copyright (c) 2005-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/common/common_node.php');
require_once('models/ecommerce/ecommerce_basket.php');
require_once('models/ecommerce/ecommerce_basket_content.php');
require_once('models/ecommerce/ecommerce_price.php');
require_once('models/ecommerce/ecommerce_order.php');

class Onxshop_Controller_Component_Ecommerce_Basket extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        $this->initModels();

        $this->basket_id = $this->getBasketId();
        $this->customer_id = (int) $_SESSION['client']['customer']['id'];
        $this->include_vat = true;
        $currency = GLOBAL_LOCALE_CURRENCY;

        $this->checkForPreviousBasket();

        // process input
        $input = $this->processInputData();

        if ($input && $this->canEditBasket()) {

            $pre_action_state = $this->Basket->getFullDetail($this->basket_id, $currency);
            $this->handleActions($input);
            $post_action_state = $this->Basket->getFullDetail($this->basket_id, $currency);
            $this->parseTrackingData($pre_action_state['items'], $post_action_state['items']);

        }

        if ($this->basket_id > 0) {

            $basket = $this->Basket->getFullDetail($this->basket_id, $currency);

            if (count($basket['items']) > 0) {

                $this->processBasketCalculations($basket);
                $this->displayBasket($basket);

                return true;

            }

        } 

        $this->displayEmptyBasket();

        return true;

    }

    /**
     * init basket
     */
     
    protected function initModels()
    {
        $node_conf = common_node::initConfiguration();
        $this->tpl->assign('NODE_CONF', $node_conf);
        
        $this->Basket = new ecommerce_basket();
        $this->Basket->setCacheable(false);

        $this->Basket_content = new ecommerce_basket_content();
        $this->Basket_content->setCacheable(false);

        $this->Order = new ecommerce_order();
        $this->Order->setCacheable(false);

        return $Basket;

    }

    /**
     * override in subclass
     */
    protected function processBasketCalculations(&$basket)
    {
        $this->Basket->calculateBasketSubTotals($basket, $this->include_vat);
        $this->Basket->calculateBasketDiscount($basket, $_SESSION['promotion_code']);
        $this->Basket->saveDiscount($basket);
        $this->Basket->calculateBasketTotals($basket);
    }

    /**
     * determine basket id
     */
    protected function getBasketId()
    {
        // from session by default
        $result = $_SESSION['basket']['id'];

        // parameter may override session
        if (is_numeric($this->GET['id'])) {

            // is the parameter from $_GET or from parent component?
            if ($_GET['id'] == $this->GET['id']) {

                // for security reasons do not allow to override the id by
                // $_GET parameter if valid security code is not present
                if (substr(makeHash($this->GET['id']), 0, 8) == $this->GET['code']) return $this->GET['id'];

            } else {

                return $this->GET['id'];

            }

        }

        return $result;
    }

    /**
     *  set basket_id in session to customer's most recent basket
     */

    protected function checkForPreviousBasket()
    {
        if (!is_numeric($this->basket_id) && $this->customer_id > 0) {
            $basket = $this->Basket->getLastLiveBasket($this->customer_id);
            if ($basket && is_numeric($basket['id'])) $this->basket_id = $_SESSION['basket']['id'] = $basket['id'];
        }
    }

    /**
     * process input data
     */

    protected function processInputData()
    {
        // don't process if some other basket on the same page did a job
        if (Zend_Registry::isRegistered('component_ecommerce_basket_processed')) return false;

        // populate basket action
        if (is_numeric($this->GET['populate_basket_from_order_id'])) return array(
            'action' => 'populate_basket_from_order_id',
            'order_id' => $this->GET['populate_basket_from_order_id']
        );

        // add to basket action
        if (is_numeric($_POST['add'])) return array(
            'action' => 'add',
            'product_variety_id' => $_POST['add'],
            'quantity' => $_POST['quantity'] > 0 ? $_POST['quantity'] : 1,
            'other_data' => is_array($_POST['other_data']) ? $_POST['other_data'] : array()
        );

        // remove from basket action
        if (is_numeric($_POST['remove'])) return array(
            'action' => 'remove',
            'basket_content_id' => $_POST['remove']
        );

        //update basket action
        if (is_array($_POST['basket_content'])) return array(
            'action' => 'update',
            'basket_content' => $_POST['basket_content']
        );

        //remove by variety id
        if (is_numeric($_POST['remove_variety_id'])) return array(
            'action' => 'remove_variety_id',
            'product_variety_id' => $_POST['remove_variety_id']
        );

        return false;
    }

    /**
     * handle actions
     */

    protected function handleActions($input)
    {
        switch ($input['action']) {

            case 'populate_basket_from_order_id':

                if (!is_numeric($this->basket_id)) $this->createBasketSession();
                $this->populateBasketFromOrderId($input['order_id']);
                break;

            case 'add':

                if (!is_numeric($this->basket_id)) $this->createBasketSession();
                if (is_numeric($input['other_data']['multiplicator'])) 
                    $price_id = $this->getCustomPriceId($input['product_variety_id'], $input['other_data']['multiplicator']);
                $this->addItem($input['product_variety_id'], $input['quantity'], $input['other_data'], $price_id);
                break;

            case 'remove':

                $this->removeItem($input['basket_content_id']);
                break;

            case 'update':

                $this->updateItems($input['basket_content']);
                break;

            case 'remove_variety_id':

                $this->removeVariety($input['product_variety_id']);
                break;
        }

        // mark basket action as processed
        Zend_Registry::set('component_ecommerce_basket_processed', true);

    }

    /**
     * create basket session
     */
     
    protected function createBasketSession()
    {
        //prepare array to insert
        $basket_data = array(
            'customer_id' => $this->customer_id, 
            'created' => date('c'),
            'note' => '', 
            'ip_address' => $_SERVER['REMOTE_ADDR'], 
            'face_value_voucher' => 0
        );
        
        //insert and return basket session array on success
        if (is_numeric($this->basket_id = $this->Basket->insert($basket_data))) {
            $_SESSION['basket'] = array('id' => $this->basket_id);
        } else {
            msg("can't create basket", 'error');
            return false;
        }
    }

    /**
     * add to basket action
     */
     
    protected function addItem($product_variety_id, $quantity, $other_data = array(), $price_id = false)
    {
        if ($this->Basket->addToBasket($this->basket_id, $product_variety_id, $quantity, $other_data, $price_id)) {
            if ($quantity == 1) msg(I18N_BASKET_ITEM_ADDED);
            else msg(str_replace('%n', $quantity, I18N_BASKET_ITEMS_ADDED));
            return true;
        }

    }
    
    /**
     * remove from basket action
     */
     
    protected function removeItem($basket_content_id)
    {
        if ($this->Basket->removeFromBasket($this->basket_id, $basket_content_id)) {
            msg(I18N_BASKET_ITEM_REMOVED);
            return true;
        } else {
            msg('Cannot remove item from basket', 'error');
            return false;
        }
    }
    
    /**
     * remove variety_id from basket action (remove basket items by variety id)
     */
     
    protected function removeVariety($variety_id)
    {
        $items = $this->Basket_content->getItems($this->basket_id);
        foreach ($items as $item) {
            if ($item['product_variety_id'] == $variety_id) $this->removeItem($item['id']);
        }
        
    }
    
    /**
     * update items in basket action
     */
     
    protected function updateItems($basket_content)
    {
        foreach ($basket_content as $basket_content_id => $item) {
            if ($item['quantity'] > 0) $this->Basket->updateBasketContent($this->basket_id, $basket_content_id, $item['quantity']);
            else $this->removeItem($basket_content_id);
        }
    }

    /**
     * repopulate basket with items from another order
     */
    
    protected function populateBasketFromOrderId($order_id)
    {
        if (!is_numeric($order_id)) return false;
        if (!is_numeric($this->basket_id)) return false;
        
        $basket_detail = $this->Basket->getBasketByOrderId($order_id);

        $items_added = array();
        foreach ($basket_detail['items'] as $item) {
            $items_added[] = $this->addItem($item['product_variety_id'], $item['quantity'], $item['other_data']);
        }

        if (in_array(true, $items_added)) msg("Items from your old order #{$order_id} were inserted into your current basket");

        return true;
    }

    /**
     * get or create custom price_id
     */
     
    protected function getCustomPriceId($product_variety_id, $multiplicator) 
    {
        if (!is_numeric($product_variety_id)) return false;
        if (!is_numeric($multiplicator)) return false;
        
        require_once('models/ecommerce/ecommerce_price.php');
        $Price = new ecommerce_price();
        $Price->setCacheable(false);
        
        $price_id = $Price->getCustomPriceIdByMultiplicator($product_variety_id, $multiplicator);
        
        if (is_numeric($price_id)) return $price_id;
        else return false;  
    }


    /**
     * calculateSubTotals
     */
    protected function displayBasket(&$basket)
    {
        $cols = 0 ;

        $items = array_reverse($basket['items']);

        foreach ($items as $item) {

            if (is_array($item['other_data']) && count($item['other_data']) > 0) $item['other_data'] = implode(", ", $item['other_data']);
            else $item['other_data'] = '';

            $this->tpl->assign('IMAGE_PRODUCT', $this->getProductImage($item['product']['id']));
            $this->tpl->assign('ITEM', $item);

            if ($basket['discount_percentage_claim'] > 0 || $basket['discount_fixed_claim'] > 0) $this->tpl->parse('content.basket.item.discount');

            $this->tpl->parse("content.basket.item");
        }

        $this->tpl->assign('VAT_NOTE', $this->include_vat ? I18N_PRICE_INC_VAT : I18N_PRICE_EX_VAT);
        $this->tpl->assign('BASKET', $basket);

        if ($basket['face_value_voucher'] > 0) $this->tpl->parse('content.basket.face_value_voucher');
        if ($basket['discount_percentage_claim'] > 0 && $basket['discount'] > 0) {
            $this->tpl->parse('content.basket.discount_percentage');
            $cols++;
        }
        if ($basket['discount_fixed_claim'] > 0 && $basket['discount'] > 0) {
            $this->tpl->parse('content.basket.discount_fixed');
            $cols++;
        }
        if ($basket['discount'] > 0) $this->tpl->parse('content.basket.discount');

        foreach ($basket['sub_totals'] as $rate => $sub_total) {
            $sub_total['rate'] = $rate;
            $this->tpl->assign('ITEM', $sub_total);
            if ($basket['discount_percentage_claim'] > 0 || $basket['discount_fixed_claim'] > 0) $this->tpl->parse('content.basket.sub_total_item.discount');
            $this->tpl->parse("content.basket.sub_total_item");
        }


        $this->tpl->assign('COLS_FULL', $cols + 7);
        $this->tpl->assign('COLS_MORE', $cols + 4);
        $this->tpl->assign('COLS_LESS', $cols + 2);
        $this->tpl->parse('content.basket');

    }

    /**
     * get product image
     */
    protected function getProductImage($product_id)
    {
        if (is_numeric($this->GET['image_size'])) $size = $this->GET['image_size'];
        else $size = 50;
        $Image_Controller = new Onxshop_Request("component/image~relation=product:role=main:width=$size:height=$size:node_id={$product_id}:limit=0,1~");
        return $Image_Controller->getContent();
    }


    /**
     * display empty basket message
     */

    protected function displayEmptyBasket()
    {
        $this->tpl->assign('EMPTY', 'empty');
        $this->tpl->parse('content.empty');
    }

    /**
     * parse tracking data (i.e. make diff of previous basket state)
     */

    public function parseTrackingData(&$pre_action_state, &$post_action_state) {

        if (!is_array($pre_action_state)) $pre_action_state = array();
        if (!is_array($post_action_state)) $post_action_state = array();

        $prevState = array();
        $currentState = array();

        // convert arrays to hashmaps
        foreach ($post_action_state as $item)
            $currentState[$item['product_variety_id']] = $item;
        foreach ($pre_action_state as $item)
            $prevState[$item['product_variety_id']] = $item;

        // check for additions
        $addedItems = array();
        foreach ($currentState as $varietyId => $item) {
            $currentQty = (int) $item['quantity'];
            $prevQty = (int) $prevState[$varietyId]['quantity'];
            if ($currentQty > $prevQty) $addedItems[$varietyId] = $currentQty - $prevQty; 
        }

        // check for removals
        $removedItems = array();
        foreach ($prevState as $varietyId => $item) {
            $prevQty = (int) $item['quantity'];
            $currentQty = (int) $currentState[$varietyId]['quantity'];
            if ($currentQty < $prevQty) $removedItems[$varietyId] = $prevQty - $currentQty; 
        }

        foreach ($addedItems as $varietyId => $qty) {
            $item = $currentState[$varietyId];
            $this->tpl->assign("ITEM", array(
                'sku' => $item['product']['variety']['sku'],
                'name' => $item['product']['name'] . ' - ' . $item['product']['variety']['name'],
                'category' => $this->getCategory($item['product']['node']['id']),
                'qty' => $qty,
                'action' => 'add'
            ));
            $this->tpl->parse("content.tracking");
        }

        foreach ($removedItems as $varietyId => $qty) {
            $item = $prevState[$varietyId];
            $this->tpl->assign("ITEM", array(
                'sku' => $item['product']['variety']['sku'],
                'name' => $item['product']['name'] . ' - ' . $item['product']['variety']['name'],
                'category' => $this->getCategory($item['product']['node']['id']),
                'qty' => $qty,
                'action' => 'remove'
            ));
            $this->tpl->parse("content.tracking");
        }

    }

    /**
     * get product page category (fot tracking purposes)
     * which is basically the root page title
     */
    public function getCategory($node_id)
    {
        require_once('models/common/common_node.php');
        $Node = new common_node();
        $pages = $Node->getFullPathDetailForBreadcrumb($node_id);

        if (!is_array($pages)) return false;
        
        $result = array();
        foreach ($pages as $page) {
            if ($page['id'] != 1 && $page['id'] != $node_id) $result[] = $page['title'];
        }

        return implode(" / ", $result);
    }

    /**
     * Check whether the basket has been
     * submitted as an order 
     */
    public function canEditBasket()
    {
        if ($this->basket_id == 0) return true; // basket wasn't created yet, editing can start
        else return ($this->Order->count("basket_id = {$this->basket_id}") == 0); // return true only if there is no order
    }
}
