<?php
/** 
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Basket extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		/**
		 * create basket object
		 */
		 
		$this->Basket = $this->initBasket();

		/**
		 * check for customer's most recent basket
		 */
		$this->checkForPreviousBasket();

		/**
		 * store basket content before making any changes for tracking pursposes
		 */
		 
		$this->storeBasketContent();
		
		/**
		 * check and process action
		 * accept post for all but populate_basket_from_order_id action
		 */
		
		$post_data = $_POST;
		
		/**
		 * check other posted data
		 */
		
		if (!is_array($post_data['other_data'])) $post_data['other_data'] = array();
		
		/**
		 * we accept only _POST data, but for repeat order facility also _GET
		 */
		 
		if (is_numeric($this->GET['populate_basket_from_order_id'])) $post_data['populate_basket_from_order_id'] = $this->GET['populate_basket_from_order_id'];
		
		$this->checkAction($post_data);
		
		/**
		 * continue shopping URL
		 */
			 
		$this->tpl->assign('CONTINUE_SHOPPING_URL', $this->getContinueShoppingURL());
		
		/**
		 * display basket or empty basket note
		 */
		 	
		if (is_numeric($_SESSION['basket']['id'])) {
			
			/**
			 * apply promotion before display
			 */
	
			if ($_SESSION['promotion_code']) {
				$this->applyPromotionCode($_SESSION['basket']['id'], $_SESSION['promotion_code']);
			}
			
			/**
			 * display
			 */
			
			$this->displayBasket($_SESSION['basket']['id']);
		
		} else {
		
			$this->displayEmptyBasket();
			
		}	

		/**
		 * destroy basket object
		 */
		 
		$this->Basket = null;
		
		return true;
	}
	
	/**
	 * init basket
	 */
	 
	public function initBasket() {
		
		/**
		 * include node configuration to setup URLs
		 */
				
		require_once('models/common/common_node.php');
		$node_conf = common_node::initConfiguration();
		$this->tpl->assign('NODE_CONF', $node_conf);
		
		/**
		 * initialize basket object
		 */
		
		if (!isset($_SESSION['client']['customer']['id'])) $_SESSION['client']['customer']['id'] = 0;
		require_once('models/ecommerce/ecommerce_basket.php');
		$Basket = new ecommerce_basket();
		$Basket->setCacheable(false);

		return $Basket;

	}

	/**
	 *  set basket_id in session to customer's most recent basket
	 */
	public function checkForPreviousBasket()
	{
		if (!is_numeric($_SESSION['basket']['id']) && $_SESSION['client']['customer']['id'] > 0) {
			$basket = $this->Basket->getLastLiveBasket($_SESSION['client']['customer']['id']);
			if ($basket && is_numeric($basket['id'])) $_SESSION['basket']['id'] = $basket['id'];
		}
	}

	/**
	 * get basket content
	 */
	 
	public function getBasketContent($basket_id) {
	
		$basket_content = $this->Basket->getContent($basket_id, GLOBAL_LOCALE_CURRENCY);
		
		return $basket_content;
	}
	
	/**
	 * check action
	 */
	
	public function checkAction($data) {
	
		/**
		 * don't process if some other basket on the same page did a job
		 */
		 
		if (Zend_Registry::isRegistered('component_ecommerce_basket_proceed')) return true;
		
		/**
		 * create the basket if doesn't exists already and we are going to add some item
		 */
		
		if (is_numeric($data['add']) || is_numeric($data['populate_basket_from_order_id'])) {
		
			if (!is_numeric($_SESSION['basket']['id']))  {
			    
			    $_SESSION['basket'] = $this->createBasketSession();
			    
			 	//one more check basket is numeric
				if (!is_numeric($_SESSION['basket']['id'])) return false;
				
			}
		}
		
		/**
		 * add or repopulate
		 */
		 
		if (is_numeric($data['add'])) {
		
			//add to basket action
			// set quantity to 1 by default
			if (!is_numeric($data['quantity'])) $data['quantity'] = 1;
			// allow to create a specific price
			if (is_numeric($data['other_data']['multiplicator'])) $price_id = $this->getCustomPriceId($data['add'], $data['other_data']['multiplicator']);
			// add item
			$this->addItem($data['add'], $data['quantity'], $data['other_data'], $price_id);
		
		} else if (is_numeric($data['populate_basket_from_order_id'])) {
		
			// repopulate items from an old order, but only no request for remove or update was sent
			if ($data['remove'] == false && $data['remove_variety_id'] == false && $data['basket_content'] == false) {
				$this->populateBasketFromOrderId($data['populate_basket_from_order_id']);
			}
		
		}

		/**
		 * other actions available for initialazed basket session only
		 * allow only one action at a time
		 */
		 		
		if (is_numeric($basket_id = $_SESSION['basket']['id'])) {
		
			//remove from basket action
			if (is_numeric($data['remove'])) $this->removeItem($basket_id, $data['remove']);
			
			//update basket action
			else if (is_array($data['basket_content'])) $this->updateItems($basket_id, $data['basket_content']);
			
			//remove by variety id
			else if (is_numeric($data['remove_variety_id'])) $this->removeVariety($basket_id, $data['remove_variety_id']);
		
		}
		
		/**
		 * mark basket action proceeed
		 */
		 
		Zend_Registry::set('component_ecommerce_basket_proceed', true);
	}
	
	/**
	 * create basket session
	 */
	 
	public function createBasketSession() {
	
		//prepare array to insert
		$basket_data = array('customer_id'=>$_SESSION['client']['customer']['id'], 'created'=>date('c'), 'note'=>'', 'ip_address'=>$_SERVER['REMOTE_ADDR'], 'discount_net'=>0);
	    
	    //insert and return basket session array on success
	    if (is_numeric($id = $this->Basket->insert($basket_data))) {
	    	$basket_session = array('id'=>$id);
	    	return $basket_session; 
	    } else {
	    	msg("can't create basket", 'error');
	    	return false;
	    }
	}
	
	/**
	 * add to basket action
	 */
	 
	public function addItem($product_variety_id, $quantity, $other_data = array(), $price_id = false) {
	
		//check basket is initialised
		if (!is_numeric($_SESSION['basket']['id'])) return false;
		
		/**
		 * add to basket
		 */
		 
		if ($this->Basket->addToBasket($_SESSION['basket']['id'], $product_variety_id, $quantity, $other_data, $price_id)) {

			msg(I18N_BASKET_ITEM_ADDED);
			
			return true;
		}

	}
	
	/**
	 * remove from basket action
	 */
	 
	public function removeItem($basket_id, $item_remove) {
	
		if ($this->Basket->removeFromBasket($basket_id, $item_remove)) {
		
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
	 
	public function removeVariety($basket_id, $variety_id) {
	
		$basket_content = $this->getBasketContent($basket_id);
		
		foreach ($basket_content['items'] as $bk=>$item) {
		
			if ($item['product_variety_id'] == $variety_id) {
				
				$this->removeItem($basket_id, $item['id']);
			
			}
		}
		
	}
	
	/**
	 * update items in basket action
	 */
	 
	public function updateItems($basket_id, $basket_content) {
		
		foreach ($basket_content as $basket_content_id=>$bc) {
		
			if ($bc['quantity'] > 0) {
				
				if ($this->Basket->updateBasketContent($basket_id, $basket_content_id, $bc['quantity'])) {
				
					msg("Item $bk has been updated.", 'ok', 2);
				
				}
				
			} else {
				
				$this->removeItem($basket_id, $basket_content_id);
				
			}
		}
	}
	
	/**
	 * display basket
	 */
	
	public function displayBasket($basket_id) {
		
		/**
		 * assign VAT note
		 */
		 
		$this->assignVATNote();
		
		/**
		 * get basket content
		 */
			
		$basket_content_data = $this->getBasketContent($basket_id);
		
		/**
		 * parse tracking data (i.e. make diff previous basket state)
		 */

		$this->parseTrackingData($basket_content_data);

		if (count($basket_content_data['items']) == 0)  {
			
			$this->displayEmptyBasket();
		
		} else {
			
			$this->tpl->assign('BASKET', $basket_content_data);
			
			foreach ($basket_content_data['items'] as $item) {
				
				//product other_data options
				if (is_array($item['other_data']) && count($item['other_data']) > 0) $item['other_data'] = implode(", ", $item['other_data']);
				else $item['other_data'] = '';
				
				//image
				$Image_Controller = new Onxshop_Request("component/image~relation=product:role=main:width=50:height=50:node_id={$item['product']['id']}:limit=0,1~");
				$this->tpl->assign('IMAGE_PRODUCT', $Image_Controller->getContent());
				
				$this->tpl->assign('ITEM', $item);
				$this->tpl->parse('content.basket.item');
			
			}
			
			/**
			 * Display discount in basket
			 */

			if ($basket_content_data['discount_net'] > 0) {
				$this->tpl->parse('content.basket.discount');
			}
			
			/**
			 * parse main block
			 */
		
			$this->tpl->parse('content.basket');
		}
	}
	
	/**
	 * display empty basket
	 */
	
	public function displayEmptyBasket() {
		
		$this->tpl->assign('EMPTY', 'empty');
		$this->tpl->parse('content.empty');
	
	}
	
	/**
	 * apply promotion code
	 */
	 
	public function applyPromotionCode($basket_id, $promotion_code, $exclude_vat = false) {
	
		require_once('models/ecommerce/ecommerce_promotion.php');
		$Promotion = new ecommerce_promotion();

		$basket_data = $this->Basket->getDetail($basket_id);
		$discount_net = $Promotion->applyPromotionCodeToBasket($promotion_code, $basket_data, $exclude_vat);
		
		if (is_numeric($discount_net)) {
			if ($this->Basket->applyDiscount($basket_id, $discount_net)) {
				return $discount_net;
			}
		}
	}
	
	/**
	 * get continue shopping URL
	 * TODO: return last product page or product detail page
	 */
	 
	public function getContinueShoppingURL() {
		return BASKET_CONTINUE_SHOPPING_URL;
	}
	
	/**
	 * repopulate basket with items from another order
	 */
	
	public function populateBasketFromOrderId($order_id) {
		
		//check order_id is numeric
		if (!is_numeric($order_id)) return false;
		
		//check basket is initialised
		if (!is_numeric($_SESSION['basket']['id'])) return false;
		
		//find basket id or get basket detail by order id
		$basket_detail = $this->Basket->getBasketByOrderId($order_id);
		
		//security check of the owner, maybe not needed because than we could display REPEAT_ORDER button also in emails, when user is not login
		/*if ($basket_detail['customer_id'] !== $_SESSION['client']['customer']['id'] &&  $_SESSION['authentication']['logon'] == 0) {
			msg('unauthorised access to repopulate basket', 'error');
			return false;
		}*/

		//iterate through and call addItem function on each line
		$items_added = array();
		foreach ($basket_detail['content']['items'] as $item) {
			$items_added[] = $this->addItem($item['product_variety_id'], $item['quantity'], $item['other_data']);
		}
		
		if (in_array(true, $items_added)) msg("Items from your old order #{$order_id} were inserted into your current basket");
		
		return true;
	}
	
	/**
	 * assign VAT note
	 */
	 
	public function assignVATNote($exclude_vat = false) {
		
		//include price configuration for VAT_NOTE
		require_once('models/ecommerce/ecommerce_price.php');
		$price_conf = ecommerce_price::initConfiguration();
		if ($price_conf['frontend_with_vat'] && !$exclude_vat) $this->tpl->assign('VAT_NOTE', I18N_PRICE_INC_VAT);
		else $this->tpl->assign('VAT_NOTE', I18N_PRICE_EX_VAT);
		
	}
	
	/**
	 * get or create custom price_id
	 */
	 
	public function getCustomPriceId($product_variety_id, $multiplicator) {
		
		if (!is_numeric($product_variety_id)) return false;
		if (!is_numeric($multiplicator)) return false;
		
		require_once('models/ecommerce/ecommerce_price.php');
		$Price = new ecommerce_price();
		
		$price_id = $Price->getCustomPriceIdByMultiplicator($product_variety_id, $multiplicator);
		
		if (is_numeric($price_id)) return $price_id;
		else return false;	
	}


	/**
	 * store basket content before making any changes for tracking pursposes
	 */

	public function storeBasketContent() {

		if (is_numeric($_SESSION['basket']['id'])) {

			$this->previousBasketContent = $this->getBasketContent($_SESSION['basket']['id']);

		} else {

			$this->previousBasketContent = false;

		}
	}

	/**
	 * parse tracking data (i.e. make diff previous basket state)
	 */

	public function parseTrackingData($basket_content_data) {

		if (!is_array($basket_content_data['items'])) $basket_content_data['items'] = array();
		if (!is_array($this->previousBasketContent['items'])) $this->previousBasketContent['items'] = array();

		$prevState = array();
		$currentState = array();

		// convert arrays to hashmaps
		foreach ($basket_content_data['items'] as $item)
			$currentState[$item['product_variety_id']] = $item;
		foreach ($this->previousBasketContent['items'] as $item)
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

	public function getCategory($node_id)
	{
		require_once('models/common/common_node.php');
		$Node = new common_node();
		$pages = $Node->getFullPathDetailForBreadcrumb($node_id);

		$result = array();
		foreach ($pages as $page) {
			if ($page['id'] != 1 && $page['id'] != $node_id) $result[] = $page['title'];
		}

		return implode(" / ", $result);
	}
}
