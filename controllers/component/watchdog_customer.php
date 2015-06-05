<?php
/** 
 * Copyright (c) 2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_watchdog.php');
require_once('models/common/common_node.php');
require_once('models/ecommerce/ecommerce_product.php');

class Onxshop_Controller_Component_Watchdog_Customer extends Onxshop_Controller {

	public function mainAction()
	{
		$this->node_conf = common_node::initConfiguration();
		$this->tpl->assign('NODE_CONF', $this->node_conf);

		$this->Watchdog = new common_watchdog();
		$this->Product = new ecommerce_product();

		$this->Watchdog->setCacheable(false); // disable db cache for front-end users

		$customer_id = (int) $_SESSION['client']['customer']['id'];

		if ($this->GET['unsubscribe']) {

			$this->processUnsubscription($this->GET['wid'], $this->GET['unsubscribe'], $customer_id);

		} else {

			$this->forceLogin($customer_id);

			$this->processSubscription($customer_id, $this->GET['product_variety_id']);
			$this->listWatchedItems($customer_id);

		}

		return true;
	}

	public function forceLogin($customer_id)
	{
		if ($customer_id == 0) {
			$redirect = "page/" . $this->node_conf['id_map-notifications'];
			if (is_numeric($this->GET['product_variety_id'])) {
				$redirect .= "?product_variety_id={$this->GET['product_variety_id']}";

				$product = $this->getProductInfo($this->GET['product_variety_id']);
				$fullname = $product['name'] . ' - ' . $product['variety']['name'];

				msg("Please login to register your interest in $fullname.");
			}
			$_SESSION['to'] = $redirect;
			onxshopGoTo("page/" . $this->node_conf['id_map-login']);
		}
	}

	public function processSubscription($customer_id, $product_variety_id)
	{
		if ($customer_id > 0 && is_numeric($product_variety_id))
		{
			$list = $this->Watchdog->listing("name = 'back_in_stock_customer' AND watched_item_id = $product_variety_id AND customer_id = $customer_id AND publish = 1");

			$data = array(
				'name' => 'back_in_stock_customer',
				'watched_item_id' => $product_variety_id,
				'customer_id' => $customer_id,
				'modified' => date("c"),
				'publish' => 1
			);

			$update = isset($list[0]['id']) && $list[0]['id'] > 0;

			if ($update) {
				$data['id'] = $list[0]['id'];
				$id = $this->Watchdog->update($data);
			} else {
				$data['created'] = date("c");
				$id = $this->Watchdog->insert($data);
			}

			if ($id > 0) {

				$product = $this->getProductInfo($product_variety_id);
				$fullname = $product['name'] . ' - ' . $product['variety']['name'];

				if ($update) msg("You have already expressed an interest in $fullname. We will notify you once it is back in stock. To remove yourself from receiving these emails please visit My Account, Notifications.");
				else msg("We will let you know as soon as our $fullname come(s) back into stock. To remove yourself from receiving these emails please visit My Account, Notifications.");

				onxshopGoTo($product['url']);

			} else {

				msg("There was an error while processing your request. Please try again later.", "error");

			}
		}
	}

	public function processUnsubscription($id, $key, $customer_id)
	{
		if ($id > 0 && strlen($key) == 32) {

			$detail = $this->Watchdog->detail($id);

			if ($detail['id'] == $id && $key == $this->Watchdog->generateKey($id)) {

				$this->Watchdog->setPublish($id, 0);
				$product = $this->getProductInfo($detail['watched_item_id']);

				msg("{$product['name']} - {$product['variety']['name']} has been removed from your watch list. We will no longer notify you regarding this product.");
				
				if ($customer_id == $detail['customer_id']) onxshopGoTo("page/" . $this->node_conf['id_map-notifications']);
				else onxshopGoTo($product['url']);

			}
		}

		msg("Invalid customer id or security key provided.", "error");

		return false;
	}

	public function listWatchedItems($customer_id)
	{
		$list = $this->Watchdog->listing("customer_id = $customer_id AND publish = 1 AND name = 'back_in_stock_customer'");

		if (count($list) > 0) {

			foreach ($list as $i => $item) {

				$item['product'] = $this->getProductInfo($item['watched_item_id']);
				$item['key'] = $this->Watchdog->generateKey($item['id']);

				$this->tpl->assign("ITEM", $item);
				if ($i < count($list) - 1) $this->tpl->parse("content.watched_items.item.divider");
				$this->tpl->parse("content.watched_items.item");
			}

			$this->tpl->parse("content.watched_items");

		} else {

			$this->tpl->parse("content.no_watched_items");
		}

	}

	protected function getProductInfo($product_variety_id)
	{

		$variety = $this->Product->getProductVarietyDetail($product_variety_id);
		$product = $this->Product->productDetail($variety['product_id']);
		$homepage = $this->Product->getProductHomepage($variety['product_id']);
		$product['url'] = translateURL('page/' . $homepage['id']);
		$Image = new Onxshop_Request("component/image&relation=product&role=main&width=120&node_id={$product['id']}&limit=0,1");

		$product['variety'] = $variety;
		$product['image'] = $Image->getContent();

		return $product;
	}

}
