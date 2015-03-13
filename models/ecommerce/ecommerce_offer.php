<?php
/**
 *
 * Copyright (c) 2013-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_offer extends Onxshop_Model {

	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'offer_group_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'product_variety_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'schedule_start'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'schedule_end'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'campaign_category_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'roundel_category_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'price_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'quantity'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'saving'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
		'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false)
		);

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "CREATE TABLE ecommerce_offer (
			id serial NOT NULL PRIMARY KEY,
			description text,
			offer_group_id integer REFERENCES ecommerce_offer_group ON UPDATE CASCADE ON DELETE RESTRICT,
			product_variety_id integer REFERENCES ecommerce_product_variety ON UPDATE CASCADE ON DELETE RESTRICT,
			schedule_start timestamp(0) without time zone,
			schedule_end timestamp(0) without time zone,
			campaign_category_id integer REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE RESTRICT,
			roundel_category_id integer REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE RESTRICT,
			price_id integer REFERENCES ecommerce_price ON UPDATE CASCADE ON DELETE RESTRICT,
			quantity integer,
			saving integer,
			created timestamp(0) without time zone,
			modified timestamp(0) without time zone,
			other_data text,
			priority integer DEFAULT 0 NOT NULL
		);
		ALTER TABLE ONLY ecommerce_offer ADD CONSTRAINT offer_group_id_product_variety_id_key UNIQUE (offer_group_id, product_variety_id);
		";
		
		return $sql;
	}

	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
	
		if (array_key_exists('ecommerce_offer', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_offer'];
		else $conf = array();
		
		if (!is_numeric($conf['campaign_category_parent_id'])) $conf['campaign_category_parent_id'] = false;
		if (!is_numeric($conf['roundel_category_parent_id'])) $conf['roundel_category_parent_id'] = false;
		
		return $conf;
	}

	/**
	 * insertOffer
	 */
	public function insertOffer($offer)
	{
		if (!is_numeric($offer['product_variety_id'])) {
			msg("Special offer has not been saved, because no product was selected.", "error");
			return false;
		}
		if (!is_numeric($offer['offer_group_id'])) {
			msg("Special offer has not been saved, because no group was selected.", "error");
			return false;
		}

		if ($offer['price_id'] > 0) {
			require_once('models/ecommerce/ecommerce_price.php');
			$Price = new ecommerce_price();
			$price = $Price->detail($offer['price_id']);
			if ($price['product_variety_id'] != $offer['product_variety_id']) {
				msg("Special offer price does not to belong to given product variety.", "error");
				return false;
			}
		}

		$detail = array(
			'product_variety_id' => $offer['product_variety_id'],
			'offer_group_id' => $offer['offer_group_id'] > 0 ? $offer['offer_group_id'] : null,
			'campaign_category_id' => $offer['campaign_category_id'] > 0 ? $offer['campaign_category_id'] : null,
			'roundel_category_id' => $offer['roundel_category_id'] > 0 ? $offer['roundel_category_id'] : null,
			'description' => $offer['description'],
			'price_id' => $offer['price_id'] > 0 ? $offer['price_id'] : null,
			'quantity' => $offer['quantity'] > 0 ? $offer['quantity'] : null,
			'saving' => $offer['saving'] > 0 ? $offer['saving'] : null,
			'created' => date("c"),
			'modified' => date("c")
		);

		return $this->Offer->insert($detail);

	}


	/**
	 * getProductIdsForOfferGroup
	 */

	public function getProductIdsForOfferGroup($offer_group_id = false, $campaign_category_id = false, 
		$roundel_category_id = false, $taxonomy_tree_ids = array(), $includeForthcoming = false) 
	{
		$offer_group_id = (int) $offer_group_id;
		$campaign_category_id = (int) $campaign_category_id;
		$roundel_category_id = (int) $roundel_category_id;

		$condition1 = '';
		$condition2 = '';
		$condition3 = '';
		$condition4 = '';
		$join = '';
		if ($offer_group_id > 0) $condition1 = "AND g.id = $offer_group_id";
		if ($campaign_category_id > 0) $condition2 = "AND o.campaign_category_id = $campaign_category_id";
		if ($roundel_category_id > 0) $condition3 = "AND o.roundel_category_id = $roundel_category_id";
		if (!$includeForthcoming) $condition4 = "AND g.schedule_start <= NOW()";
		if (count($taxonomy_tree_ids) > 0) {
			foreach ($taxonomy_tree_ids as &$taxonomy_tree_id) $taxonomy_tree_id = 1 * $taxonomy_tree_id; // sanitize input
			$join = "INNER JOIN ecommerce_product_taxonomy AS t ON t.node_id = v.product_id AND t.taxonomy_tree_id IN (" . implode(',', $taxonomy_tree_ids) . ")";
		}

		$sql = "SELECT DISTINCT v.product_id, o.priority, o.created
			FROM ecommerce_offer_group AS g 
			INNER JOIN ecommerce_offer AS o ON o.offer_group_id = g.id $condition2 $condition3
			INNER JOIN ecommerce_product_variety AS v ON v.id = o.product_variety_id
			$join
			WHERE g.schedule_end >= NOW() AND g.publish = 1 $condition1 $condition4
			ORDER BY o.priority DESC, o.created DESC
			";

		$rows = $this->executeSql($sql);

		$result = array();
		foreach ($rows as $item) $result[] = $item['product_id'];

		return $result;
	}	
	
	/**
	 * getActiveOffers
	 */

	public function getActiveOffers($includeForthcoming = false) 
	{
		if (!$includeForthcoming) $condition = "AND g.schedule_start <= NOW()";

		$sql = "SELECT o.id AS offer_id,
				v.product_id AS product_id,
				v.id AS product_variety_id,
				o.price_id AS price_id,
				p.value AS price,
				p.currency_code AS currency_code,
				o.quantity AS quantity,
				o.saving AS saving,
				o.campaign_category_id AS campaign_category_id,
				o.roundel_category_id AS roundel_category_id,
				cl.description AS campaign_category,
				rl.description AS roundel_category,
				g.id AS group_id,
				g.title AS group_title,
				g.schedule_start AS group_schedule_start,
				g.schedule_end AS group_schedule_end
				
			FROM ecommerce_offer_group AS g 
			INNER JOIN ecommerce_offer AS o ON o.offer_group_id = g.id
			INNER JOIN ecommerce_product_variety AS v ON v.id = o.product_variety_id
			LEFT JOIN ecommerce_price AS p ON p.id = o.price_id
			INNER JOIN common_taxonomy_tree AS ct ON ct.id = o.campaign_category_id
			INNER JOIN common_taxonomy_tree AS rt ON rt.id = o.roundel_category_id
			INNER JOIN common_taxonomy_label AS cl ON cl.id = ct.label_id
			INNER JOIN common_taxonomy_label AS rl ON rl.id = rt.label_id
			WHERE g.schedule_end >= NOW() AND g.publish = 1 $condition";

		$result = $this->executeSql($sql);
		return $result;
	}	

}
