BEGIN;
ALTER TABLE ecommerce_delivery RENAME value_netto TO value_net;
ALTER TABLE ecommerce_invoice RENAME goods_netto TO goods_net;
ALTER TABLE ecommerce_invoice RENAME delivery_netto TO delivery_net;
ALTER TABLE ecommerce_product_variety RENAME weight_bruto TO weight_gross;
ALTER TABLE common_node RENAME node_type TO node_group;
ALTER TABLE common_node RENAME layout_template TO node_controller;

ALTER TABLE common_node RENAME display_in TO display_in_menu;
UPDATE common_node SET display_in_menu = 0 WHERE display_in_menu = 'no_menu';
UPDATE common_node SET display_in_menu = 1 WHERE display_in_menu = '' OR display_in_menu = 'itself';
UPDATE common_node SET display_in_menu = 2 WHERE display_in_menu = 'no_link';
ALTER TABLE common_node ALTER COLUMN display_in_menu DROP DEFAULT;
ALTER TABLE common_node ALTER COLUMN display_in_menu TYPE smallint USING CAST(display_in_menu as integer), ALTER COLUMN display_in_menu SET NOT NULL, ALTER COLUMN display_in_menu SET DEFAULT 1;

ALTER TABLE common_taxonomy_tree ADD COLUMN priority smallint NOT NULL DEFAULT 0;
ALTER TABLE common_taxonomy_tree ADD COLUMN publish smallint NOT NULL DEFAULT 1;

ALTER TABLE ecommerce_product_variety RENAME code TO sku;
ALTER TABLE ecommerce_product_variety ADD COLUMN condition smallint NOT NULL DEFAULT 0;

UPDATE ecommerce_delivery_carrier_zone SET carrier_id = 2;

DROP TABLE ecommerce_product_to_node;

ALTER TABLE ecommerce_promotion ADD COLUMN limit_delivery_country_id smallint NOT NULL DEFAULT 0;
ALTER TABLE ecommerce_promotion ADD COLUMN limit_delivery_carrier_id smallint NOT NULL DEFAULT 0;

DELETE FROM common_node WHERE node_controller = 'product_list';

ALTER TABLE common_comment ALTER COLUMN parent DROP NOT NULL;
ALTER TABLE common_comment ALTER COLUMN node_id DROP NOT NULL;
UPDATE common_comment SET parent = NULL WHERE parent = 0;

ALTER TABLE ecommerce_product_review ALTER COLUMN parent DROP NOT NULL;
ALTER TABLE ecommerce_product_review ALTER COLUMN node_id DROP NOT NULL;

DELETE FROM ecommerce_product_to_product WHERE EXISTS (
	SELECT id 
	FROM ecommerce_product_to_product  i
	WHERE i.id = ecommerce_product_to_product .id
	AND i.ctid > ecommerce_product_to_product.ctid
);

ALTER TABLE ONLY ecommerce_product_to_product ADD CONSTRAINT ecommerce_product_to_product_pkey PRIMARY KEY (id);

DELETE FROM ecommerce_product_to_product WHERE EXISTS (
	SELECT id 
	FROM ecommerce_product_to_product  i
	WHERE i.product_id = ecommerce_product_to_product.product_id 
	AND i.related_product_id = ecommerce_product_to_product.related_product_id
	AND i.ctid > ecommerce_product_to_product.ctid
);

ALTER TABLE ecommerce_product_to_product ADD CONSTRAINT product_id_related_product_id_key UNIQUE (product_id, related_product_id);

DELETE FROM ecommerce_product_taxonomy WHERE EXISTS (
	SELECT id 
	FROM ecommerce_product_taxonomy  i
	WHERE i.node_id = ecommerce_product_taxonomy.node_id 
	AND i.taxonomy_tree_id = ecommerce_product_taxonomy.taxonomy_tree_id
	AND i.ctid > ecommerce_product_taxonomy.ctid
);

ALTER TABLE common_node_taxonomy ADD CONSTRAINT node_node_id_taxonomy_tree_id_key UNIQUE (node_id, taxonomy_tree_id);
ALTER TABLE ecommerce_product_taxonomy ADD CONSTRAINT product_node_id_taxonomy_tree_id_key UNIQUE (node_id, taxonomy_tree_id);
ALTER TABLE ecommerce_product_variety_taxonomy ADD CONSTRAINT product_variety_node_id_taxonomy_tree_id_key UNIQUE (node_id, taxonomy_tree_id);
ALTER TABLE ecommerce_delivery_carrier_zone_to_country ADD CONSTRAINT country_id_zone_id_key UNIQUE (country_id, zone_id);

ALTER TABLE common_taxonomy_tree ALTER COLUMN parent DROP NOT NULL;
UPDATE common_taxonomy_tree SET parent = NULL WHERE parent = 0;
DELETE FROM common_taxonomy_tree WHERE id = 0;

UPDATE common_node SET parent = NULL WHERE id = 0;

UPDATE common_node SET node_group = 'content' WHERE node_group = 'layout' AND node_controller = 'shared';
UPDATE common_node SET component = regexp_replace(component, 's:15:"layout_template"', 's:15:"node_controller"') WHERE node_group = 'content' AND node_controller = 'contact_form';

UPDATE common_node SET layout_style = 'fibonacci-2-5' WHERE layout_style = 'twenty-eighty';
UPDATE common_node SET layout_style = 'fibonacci-1-2' WHERE layout_style = 'thirty-seventy';
UPDATE common_node SET layout_style = 'fibonacci-1-1' WHERE layout_style = 'fifty-fifty';
UPDATE common_node SET layout_style = 'fibonacci-2-1' WHERE layout_style = 'seventy-thirty';
UPDATE common_node SET layout_style = 'fibonacci-5-2' WHERE layout_style = 'eighty-twenty';

UPDATE common_node SET parent_container = 2 WHERE parent_container = 5;

UPDATE common_node SET css_class = 'pageLogin' WHERE id = 8;

ALTER TABLE ecommerce_transaction ADD COLUMN type varchar(255);
ALTER TABLE ecommerce_transaction ADD COLUMN status smallint;
ALTER TABLE ecommerce_product ADD COLUMN name_aka varchar(255);
ALTER TABLE ecommerce_product_variety ADD COLUMN wholesale smallint;

UPDATE common_node SET parent_container = 3 WHERE parent_container = 2 AND parent IN (SELECT id FROM common_node WHERE node_group = 'layout');
UPDATE common_node SET parent_container = 2 WHERE parent_container = 1 AND parent IN (SELECT id FROM common_node WHERE node_group = 'layout');
UPDATE common_node SET parent_container = 1 WHERE parent_container = 0 AND parent IN (SELECT id FROM common_node WHERE node_group = 'layout');



UPDATE common_node SET require_ssl = 1 WHERE node_controller = 'commerce';
UPDATE common_node SET require_ssl = 0 WHERE id = 6;
UPDATE common_node SET require_login = 1 WHERE id = 7 OR id = 10 OR id = 11 OR id = 12 OR id = 15 OR id = 16 OR id = 17 OR id = 18 OR id = 19;
UPDATE common_node SET node_controller = 'default' WHERE node_controller = 'commerce';

UPDATE common_node SET display_in_menu = 0 WHERE id = 2 OR id = 3;


INSERT INTO common_node VALUES (89, 'Select Delivery Method', 'content', 'component', 7, 1, 100, NULL, NULL, '', '', '', '', '2010-04-18 01:34:49', '2010-04-18 11:10:57', 1, 1, 1000, '', 0, 'N;', '', '', 'a:3:{s:8:"template";s:30:"ecommerce/delivery_option.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}', 'N;', 1, NULL, NULL, 0, '', 0, 0);

INSERT INTO common_node VALUES (90, 'Newsletter', 'page', 'default', 4, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2010-04-18 11:19:18', '2010-04-18 11:19:18', 1, 0, 1000, NULL, 0, NULL, '', 'fibonacci-2-1', NULL, NULL, 1, NULL, NULL, 0, '', 0, 0);

INSERT INTO common_node VALUES (91, 'Newsletter Subscribe', 'content', 'component', 90, 1, 0, NULL, NULL, '', '', '', '', '2010-04-18 11:20:58', '2010-04-18 11:21:14', 1, 1, 1000, '', 0, 'N;', '', '', 'a:3:{s:8:"template";s:32:"client/newsletter_subscribe.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}', 'N;', 0, NULL, NULL, 0, '', 0, 0);

INSERT INTO common_node VALUES (92, 'Unsubscribe', 'page', 'default', 91, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, '2010-04-18 11:21:40', '2010-04-18 11:21:40', 1, 1, 1000, NULL, 0, NULL, '', 'fibonacci-2-1', NULL, NULL, 1, NULL, NULL, 0, '', 0, 0);

INSERT INTO common_node VALUES (93, 'Newsletter Unsubscribe', 'content', 'component', 92, 1, 0, NULL, NULL, '', '', '', '', '2010-04-18 11:22:40', '2010-04-18 11:22:56', 1, 1, 1000, '', 0, 'N;', '', '', 'a:3:{s:8:"template";s:34:"client/newsletter_unsubscribe.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}', 'N;', 0, NULL, NULL, 0, '', 0, 0);
SELECT pg_catalog.setval('common_node_id_seq', (SELECT max(id) FROM common_node), true);
COMMIT;
