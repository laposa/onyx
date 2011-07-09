BEGIN;
ALTER TABLE ecommerce_delivery ADD COLUMN weight integer NOT NULL DEFAULT 0;

ALTER TABLE ecommerce_product_type ADD COLUMN publish integer DEFAULT 1 NOT NULL;

INSERT INTO international_currency_rate VALUES (nextval('international_currency_rate_id_seq'::regclass), 'USD', 'CZK', 'cnb', '2009-08-27 01:13:59', 17.80100000);
INSERT INTO international_currency_rate VALUES (nextval('international_currency_rate_id_seq'::regclass), 'GBP', 'CZK', 'cnb', '2009-08-27 01:16:11', 28.93000000);
INSERT INTO international_currency_rate VALUES (nextval('international_currency_rate_id_seq'::regclass), 'EUR', 'CZK', 'cnb', '2009-08-27 01:17:18', 25.40500000);
INSERT INTO international_currency_rate VALUES (nextval('international_currency_rate_id_seq'::regclass), 'AUD', 'CZK', 'cnb', '2009-09-30 23:17:18', 15.15600000);
INSERT INTO international_currency_rate VALUES (nextval('international_currency_rate_id_seq'::regclass), 'JPY', 'CZK', 'cnb', '2009-09-30 23:17:18', 0.19186000);
INSERT INTO international_currency_rate VALUES (nextval('international_currency_rate_id_seq'::regclass), 'CAD', 'CZK', 'cnb', '2009-09-30 23:17:18', 16.01200000);
INSERT INTO international_currency_rate VALUES (nextval('international_currency_rate_id_seq'::regclass), 'HKD', 'CZK', 'cnb', '2009-09-30 23:17:18', 2.21600000);
INSERT INTO international_currency_rate VALUES (nextval('international_currency_rate_id_seq'::regclass), 'NZD', 'CZK', 'cnb', '2009-09-30 23:17:18', 12.39900000);

/*first update wrong iso_code2 for Madeira (is in conflict with new country Montenegro)*/
UPDATE international_country SET iso_code2 = 'XM' WHERE id = 74;
/*Add new countries Montenegro and Serbia*/
INSERT INTO international_country (name, iso_code2, iso_code3, eu_status) VALUES ('Montenegro', 'ME', 'MNE', FALSE);
INSERT INTO international_country (name, iso_code2, iso_code3, eu_status) VALUES ('Serbia', 'RS', 'SRB', FALSE);
/*insert Montenegro and Serbia to ecommerce_delivery_carrier_zone_to_country*/
INSERT INTO ecommerce_delivery_carrier_zone_to_country (country_id, zone_id) VALUES (240, 8);
INSERT INTO ecommerce_delivery_carrier_zone_to_country  (country_id, zone_id) VALUES (241, 8);
/*update EU status for Romania (175) and Bulgaria (33)*/
UPDATE international_country SET eu_status = TRUE WHERE id = 175 OR id = 33;
/*added to project_skeleton-1_2*/

ALTER TABLE ecommerce_product_variety ADD COLUMN ean13 varchar(255);
ALTER TABLE ecommerce_product_variety ADD COLUMN upc varchar(255);

ALTER TABLE common_node DROP body_attributes;
ALTER TABLE common_node ADD COLUMN require_ssl smallint NOT NULL DEFAULT 0;

UPDATE common_node SET priority = 15 WHERE id = 88;
UPDATE common_node SET layout_template = 'default' WHERE layout_template = 'default_advanced';
UPDATE common_node SET parent_container =0 WHERE parent IN (SELECT id FROM common_node WHERE node_type = 'layout' AND layout_template = '1column');

UPDATE common_node SET component = 'a:3:{s:8:"template";s:19:"client/address.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:22:"ecommerce/address.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}';
UPDATE common_node SET component = 'a:3:{s:8:"template";s:24:"client/address_edit.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:27:"ecommerce/address_edit.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}';
UPDATE common_node SET component = 'a:3:{s:8:"template";s:17:"client/login.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:20:"ecommerce/login.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}';
UPDATE common_node SET component = 'a:3:{s:8:"template";s:22:"client/user_prefs.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:25:"ecommerce/user_prefs.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}';
UPDATE common_node SET component = 'a:3:{s:8:"template";s:24:"client/registration.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:27:"ecommerce/registration.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}';
UPDATE common_node SET component = 'a:3:{s:8:"template";s:26:"client/password_reset.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:29:"ecommerce/password_reset.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}';
UPDATE common_node SET component = 'a:3:{s:8:"template";s:30:"client/registration_start.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:33:"ecommerce/registration_start.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}';
UPDATE common_node SET component = 'a:3:{s:8:"template";s:24:"client/registration.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:27:"ecommerce/registration.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}';

UPDATE common_node SET component = 'a:3:{s:8:"template";s:25:"ecommerce/order_list.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:21:"ecommerce/orders.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}';

UPDATE common_node SET component = 'a:3:{s:8:"template";s:37:"ecommerce/payment/protx_callback.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:37:"ecommerce/payment_callback_protx.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}';

UPDATE common_node SET component = 'a:3:{s:8:"template";s:37:"ecommerce/payment/protx_callback.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:30:"ecommerce/payment_failure.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}';

UPDATE common_node SET component = 'a:3:{s:8:"template";s:37:"ecommerce/payment/protx_callback.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:30:"ecommerce/payment_success.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}';

UPDATE common_node SET component = 'a:3:{s:8:"template";s:0:"";s:10:"controller";s:39:"ecommerce/payment/worldpay_callback.php";s:9:"parameter";s:0:"";}'
WHERE component = 'a:3:{s:8:"template";s:0:"";s:10:"controller";s:39:"ecommerce/payment_callback_worldpay.php";s:9:"parameter";s:0:"";}';

ALTER TABLE client_customer ADD COLUMN verified_email_address smallint NOT NULL DEFAULT 0;

ALTER TABLE ecommerce_delivery_carrier ADD COLUMN free_delivery_map text;


COMMIT;


