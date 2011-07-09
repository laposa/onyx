BEGIN;
UPDATE common_node SET display_title = 0 WHERE title = 'Edit your title here';
UPDATE common_node SET display_title = 0 WHERE title ~* '^layout [0-9]*$';
UPDATE common_node SET display_title = 0 WHERE title ~* '^content [0-9]*$';
UPDATE common_node SET layout_template = 'feed' WHERE layout_template = 'RSS';

ALTER TABLE common_node ADD COLUMN display_breadcrumb smallint NOT NULL DEFAULT 0;
ALTER TABLE common_node ADD COLUMN browser_title varchar(255) NOT NULL DEFAULT '';
ALTER TABLE common_node ADD COLUMN link_to_node_id integer NOT NULL DEFAULT 0;
ALTER TABLE common_node ADD FOREIGN KEY (link_to_node_id) REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE client_customer ADD COLUMN account_type smallint NOT NULL DEFAULT 0;
ALTER TABLE client_customer ADD COLUMN agreed_with_latest_t_and_c smallint NOT NULL DEFAULT 0;

UPDATE client_customer SET title_before = 'n/a' WHERE title_before = '';

ALTER TABLE common_comment ADD COLUMN rating smallint DEFAULT 0;
ALTER TABLE ecommerce_product_review ADD COLUMN rating smallint DEFAULT 0;

ALTER TABLE ecommerce_product ADD COLUMN availability smallint NOT NULL DEFAULT 0;
/*option to each product (0 on stock, 7 - 1week, 14 - 2weeks, 21 - 3weeks, 31 - month)*/

ALTER TABLE ecommerce_product_variety ADD COLUMN display_permission integer NOT NULL DEFAULT 0;
ALTER TABLE shipping_wz_zone_price RENAME TO ecommerce_delivery_carrier_zone_price;
ALTER TABLE shipping_wz_country_to_zone RENAME TO ecommerce_delivery_carrier_zone_to_country;
ALTER TABLE shipping_wz_zone RENAME TO ecommerce_delivery_carrier_zone;
CREATE TABLE ecommerce_delivery_carrier (
    id serial NOT NULL PRIMARY KEY,
    title varchar(255) ,
    description text ,
    limit_list_countries text ,
    limit_list_products text ,
    limit_list_product_types text ,
    limit_order_value decimal(12,5) NOT NULL DEFAULT 0,
    fixed_value decimal(12,5) NOT NULL DEFAULT 0,
	fixed_percentage decimal(5,2) NOT NULL DEFAULT 0,
    priority smallint NOT NULL DEFAULT 0,
    publish smallint NOT NULL DEFAULT 1
);

INSERT INTO ecommerce_delivery_carrier (id, title, publish, fixed_value) VALUES (1, 'Standard', 0, 5);
INSERT INTO ecommerce_delivery_carrier (id, title, publish) VALUES (2, 'Royal Mail 1st Class Post', 1);
INSERT INTO ecommerce_delivery_carrier (id, title, publish, fixed_value, limit_list_countries) VALUES (3, 'DHL Courier', 1, 7, '222');
INSERT INTO ecommerce_delivery_carrier (id, title, publish) VALUES (4, 'UPS', 0);
INSERT INTO ecommerce_delivery_carrier (id, title, publish) VALUES (5, 'Courier', 0);
INSERT INTO ecommerce_delivery_carrier (id, title, publish) VALUES (6, 'Download', 0);

SELECT pg_catalog.setval('ecommerce_delivery_carrier_id_seq', (SELECT max(id) FROM ecommerce_delivery_carrier), true);

ALTER TABLE ecommerce_delivery_carrier_zone ADD COLUMN carrier_id integer NOT NULL DEFAULT 1;
ALTER TABLE ecommerce_delivery_carrier_zone ADD FOREIGN KEY (carrier_id) REFERENCES ecommerce_delivery_carrier ON UPDATE CASCADE ON DELETE CASCADE;

UPDATE ecommerce_delivery SET type = '1' WHERE type = 'standard';
UPDATE ecommerce_delivery SET type = '2' WHERE type = 'royal_mail';
UPDATE ecommerce_delivery SET type = '3' WHERE type = 'dhl';
UPDATE ecommerce_delivery SET type = '5' WHERE type = 'courier';

SELECT DISTINCT type FROM ecommerce_delivery;
ALTER TABLE ecommerce_delivery RENAME type TO carrier_id;
ALTER TABLE ecommerce_delivery ALTER COLUMN carrier_id TYPE integer USING CAST(carrier_id as integer);

ALTER TABLE ecommerce_delivery ADD FOREIGN KEY (carrier_id) REFERENCES ecommerce_delivery_carrier ON UPDATE CASCADE ON DELETE CASCADE;
/*fix component/ecommerce/delivery_option.php, fix conf/ecommerce_delivery.php*/
CREATE TABLE ecommerce_promotion (
    id serial NOT NULL PRIMARY KEY,
    title varchar(255) ,
    description text ,
    publish smallint NOT NULL DEFAULT 1,
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
    customer_account_type smallint NOT NULL DEFAULT 0,
    code_pattern varchar(255) NOT NULL, 
	discount_fixed_value decimal(12,5) NOT NULL DEFAULT 0,
	discount_percentage_value decimal(5,2) NOT NULL DEFAULT 0,
    discount_free_delivery smallint NOT NULL DEFAULT 0,
    uses_per_coupon integer NOT NULL DEFAULT 0 ,
    uses_per_customer smallint NOT NULL DEFAULT 0,
    limit_list_products text ,
    other_data text  
);

CREATE TABLE ecommerce_promotion_code (
    id serial NOT NULL PRIMARY KEY,
    promotion_id integer NOT NULL REFERENCES ecommerce_promotion ON UPDATE CASCADE ON DELETE RESTRICT,
    code varchar(255),
    order_id integer  NOT NULL REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT
);



/*check all promotions codes in ecommerce_order and insert into promotion_code table, than remove*/

/*START only for jing*/
/*INSERT INTO ecommerce_promotion (title, code_pattern, discount_percentage_value) VALUES ('White Stuff', '168[0-5]{1}[0-5]{1}[0-9]{2}', 15);
INSERT INTO ecommerce_promotion (title, code_pattern, discount_percentage_value) VALUES ('Twitter Promotion', '168560[1-7]{1}', 15);
SELECT id, promotion_code FROM ecommerce_order WHERE promotion_code != '' ORDER BY id ASC;
ALTER TABLE ecommerce_order DROP COLUMN shipping_id;
DROP TABLE ecommerce_shipping;
ALTER TABLE client_company ADD COLUMN registration_no varchar(255);
ALTER TABLE client_company ADD COLUMN other_data text;
*/
/*END only for jing*/

ALTER TABLE ecommerce_order DROP COLUMN promotion_code;

COMMIT;
