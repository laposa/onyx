SET client_encoding = 'UNICODE';
BEGIN;

CREATE TABLE ecommerce_product_type (
    id serial NOT NULL PRIMARY KEY,
    name character varying(255),
    vat numeric DEFAULT 0 NOT NULL,
	publish integer DEFAULT 1 NOT NULL
);

INSERT INTO ecommerce_product_type VALUES (1, 'Hardware', 17.5, 0);
INSERT INTO ecommerce_product_type VALUES (2, 'Software', 17.5, 0);
INSERT INTO ecommerce_product_type VALUES (3, 'Energy', 5, 0);
INSERT INTO ecommerce_product_type VALUES (4, 'Software (only download)', 17.5, 0);
INSERT INTO ecommerce_product_type VALUES (5, 'Documents  (download)', 17.5, 0);
INSERT INTO ecommerce_product_type VALUES (6, 'books', 0, 0);
INSERT INTO ecommerce_product_type VALUES (7, 'Food', 17.5, 0);
INSERT INTO ecommerce_product_type VALUES (8, 'Food BIO', 5, 0);
INSERT INTO ecommerce_product_type VALUES (9, 'Generic 1', 17.5, 1);
INSERT INTO ecommerce_product_type VALUES (10, 'Generic 2', 5, 1);
INSERT INTO ecommerce_product_type VALUES (11, 'Generic 0', 0, 1);

SELECT pg_catalog.setval('ecommerce_product_type_id_seq', (SELECT max(id) FROM ecommerce_product_type), true);

CREATE TABLE ecommerce_product (
    id serial NOT NULL PRIMARY KEY,
    name character varying(255),
    teaser text,
    description text,
    product_type_id integer REFERENCES ecommerce_product_type ON UPDATE CASCADE ON DELETE CASCADE,
    url text,
    priority integer DEFAULT 0 NOT NULL,
    publish integer DEFAULT 0 NOT NULL,
    other_data text,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
	availability smallint NOT NULL DEFAULT 0,
	name_aka varchar(255)
);

CREATE TABLE ecommerce_product_taxonomy ( 
	id serial NOT NULL PRIMARY KEY,
	node_id int NOT NULL REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE CASCADE,
	taxonomy_tree_id int NOT NULL REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE CASCADE
);

ALTER TABLE ecommerce_product_taxonomy ADD CONSTRAINT product_node_id_taxonomy_tree_id_key UNIQUE (node_id, taxonomy_tree_id);

CREATE TABLE ecommerce_product_variety (
    id serial NOT NULL PRIMARY KEY,
    name character varying(255),
    product_id integer REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE CASCADE,
    sku character varying(255),
    weight integer,
    weight_gross integer,
    stock integer,
    priority integer DEFAULT 0 NOT NULL,
    description text,
    other_data text,
    width integer DEFAULT 0 NOT NULL,
    height integer DEFAULT 0 NOT NULL,
    depth integer DEFAULT 0 NOT NULL,
    diameter integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
    publish smallint DEFAULT 0 NOT NULL,
	display_permission integer NOT NULL DEFAULT 0,
	condition smallint NOT NULL DEFAULT 0,
	wholesale smallint
);

ALTER TABLE "ecommerce_product_variety" ADD UNIQUE ("sku");

CREATE TABLE ecommerce_price (
    id serial NOT NULL PRIMARY KEY,
    product_variety_id integer REFERENCES ecommerce_product_variety ON UPDATE CASCADE ON DELETE CASCADE,
    currency_code character(3),
    value numeric(12,5),
    "type" character varying(255),
    date timestamp(0) without time zone
);

CREATE TABLE ecommerce_basket (
    id serial NOT NULL PRIMARY KEY,
    customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
    created timestamp(0) without time zone,
    note text,
    ip_address character varying(255),
    discount_net decimal(12,5)
);


CREATE TABLE ecommerce_basket_content (
    id serial NOT NULL PRIMARY KEY,
    basket_id integer REFERENCES ecommerce_basket ON UPDATE CASCADE ON DELETE CASCADE,
    product_variety_id integer REFERENCES ecommerce_product_variety ON UPDATE CASCADE ON DELETE RESTRICT,
    quantity integer,
    price_id integer REFERENCES ecommerce_price ON UPDATE RESTRICT ON DELETE RESTRICT,
    other_data text,
    product_type_id smallint REFERENCES ecommerce_product_type ON UPDATE CASCADE ON DELETE RESTRICT
);


CREATE TABLE ecommerce_order (
    id serial NOT NULL PRIMARY KEY,
    basket_id integer REFERENCES ecommerce_basket ON UPDATE CASCADE ON DELETE RESTRICT,
    invoices_address_id integer REFERENCES client_address ON UPDATE CASCADE ON DELETE RESTRICT,
    delivery_address_id integer REFERENCES client_address ON UPDATE CASCADE ON DELETE RESTRICT,
    other_data text,
    status integer,
    note_customer text,
    note_backoffice text,
    php_session_id character varying(32),
    referrer character varying(255),
    payment_type character varying(255)
);

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

INSERT INTO ecommerce_delivery_carrier VALUES (1, 'Standard');
INSERT INTO ecommerce_delivery_carrier VALUES (2, 'Royal Mail 1st Class Post');
INSERT INTO ecommerce_delivery_carrier VALUES (3, 'DHL Courier');
INSERT INTO ecommerce_delivery_carrier VALUES (4, 'UPS');
INSERT INTO ecommerce_delivery_carrier VALUES (5, 'Courier');
INSERT INTO ecommerce_delivery_carrier VALUES (6, 'Download');

SELECT pg_catalog.setval('ecommerce_delivery_carrier_id_seq', (SELECT max(id) FROM ecommerce_delivery_carrier), true);

CREATE TABLE ecommerce_delivery_carrier_zone (
    id serial PRIMARY KEY,
    name varchar(255),
    carrier_id integer NOT NULL DEFAULT 1 REFERENCES ecommerce_delivery_carrier ON UPDATE CASCADE ON DELETE CASCADE;
);

CREATE TABLE ecommerce_delivery_carrier_zone_to_country (

    id serial PRIMARY KEY,
    country_id int NOT NULL REFERENCES international_country ON UPDATE CASCADE ON DELETE CASCADE,
    zone_id int NOT NULL REFERENCES ecommerce_delivery_carrier_zone ON UPDATE CASCADE ON DELETE CASCADE

);

ALTER TABLE ecommerce_delivery_carrier_zone_to_country ADD CONSTRAINT country_id_zone_id_key UNIQUE (country_id, zone_id);

CREATE TABLE ecommerce_delivery_carrier_zone_price (

    id serial PRIMARY KEY,
    zone_id int NOT NULL REFERENCES ecommerce_delivery_carrier_zone ON UPDATE CASCADE ON DELETE CASCADE,
    weight int ,
    price numeric(9,2) ,
    currency_code char(3)

);

CREATE TABLE ecommerce_delivery (
    id serial NOT NULL PRIMARY KEY,
    order_id int REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT,
    carrier_id integer REFERENCES ecommerce_delivery_carrier ON UPDATE CASCADE ON DELETE CASCADE ,
    value_net decimal(12,5) ,
    vat decimal(12,5) ,
    vat_rate decimal(12,5) ,
    required_datetime timestamp(0) without time zone,
    note_customer text ,
    note_backoffice text ,
    other_data text,
	weight integer NOT NULL DEFAULT 0
);

CREATE TABLE ecommerce_order_log (
    id serial NOT NULL PRIMARY KEY,
    order_id integer REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT,
    status integer,
    datetime timestamp(0) without time zone
);

CREATE TABLE ecommerce_invoice ( 
	id serial NOT NULL PRIMARY KEY,
	order_id integer REFERENCES ecommerce_order ON UPDATE RESTRICT ON DELETE RESTRICT ,
	goods_net decimal(12,5) ,
	goods_vat_sr decimal(12,5) ,
	goods_vat_rr decimal(12,5) ,
	delivery_net decimal(12,5) ,
	delivery_vat decimal(12,5) ,
	payment_amount decimal(12,5) ,
	payment_type character varying(255) ,
	created timestamp(0) without time zone NOT NULL DEFAULT NOW(),
	modified timestamp(0) without time zone NOT NULL DEFAULT NOW(),
	status smallint ,
	other_data text,
	basket_detail text,
	customer_name character varying(255) ,
	customer_email character varying(255) ,
	address_invoice text,
	address_delivery text
);

CREATE TABLE ecommerce_transaction (
    id serial NOT NULL PRIMARY KEY,
    order_id integer REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT,
    pg_data text,
    currency_code character(3),
    amount numeric(12,5),
    created timestamp(0) without time zone,
	type varchar(255),
	status smallint
);


CREATE TABLE ecommerce_product_to_product ( 
	id serial NOT NULL PRIMARY KEY,
	product_id int REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE CASCADE,
	related_product_id int REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE CASCADE 
);

ALTER TABLE ecommerce_product_to_product ADD CONSTRAINT product_id_related_product_id_key UNIQUE (product_id, related_product_id);

CREATE TABLE ecommerce_product_image ( 
	id serial NOT NULL PRIMARY KEY,
	src character varying(255),
	role character varying(255),
	node_id int NOT NULL REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE CASCADE,
	title character varying(255),
	description text,
	priority integer DEFAULT 0 NOT NULL,
	modified timestamp(0) without time zone,
	author integer
);

CREATE TABLE ecommerce_product_variety_image ( 
	id serial NOT NULL PRIMARY KEY,
	src character varying(255),
	role character varying(255),
	node_id int NOT NULL REFERENCES ecommerce_product_variety ON UPDATE CASCADE ON DELETE CASCADE,
	title character varying(255),
	description text,
	priority integer DEFAULT 0 NOT NULL,
	modified timestamp(0) without time zone,
	author integer
);

CREATE TABLE ecommerce_product_variety_taxonomy ( 
	id serial NOT NULL PRIMARY KEY,
	node_id int NOT NULL REFERENCES ecommerce_product_variety ON UPDATE CASCADE ON DELETE CASCADE,
	taxonomy_tree_id int NOT NULL REFERENCES common_taxonomy_tree ON UPDATE CASCADE ON DELETE CASCADE
);

ALTER TABLE ecommerce_product_variety_taxonomy ADD CONSTRAINT product_variety_node_id_taxonomy_tree_id_key UNIQUE (node_id, taxonomy_tree_id);

CREATE TABLE ecommerce_product_review (
    id serial PRIMARY KEY NOT NULL,
    parent int REFERENCES ecommerce_product_review ON UPDATE CASCADE ON DELETE CASCADE,
    node_id int REFERENCES ecommerce_product ON UPDATE CASCADE ON DELETE RESTRICT,
    title varchar(255) ,
    content text ,
    author_name varchar(255) ,
    author_email varchar(255) ,
    author_website varchar(255) ,
	author_ip_address varchar(255),
    customer_id int NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
    created timestamp(0) default now(),
    publish smallint,
	rating default 0
);

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
    other_data text,
	limit_delivery_country_id smallint NOT NULL DEFAULT 0,
	limit_delivery_carrier_id smallint NOT NULL DEFAULT 0
);

CREATE TABLE ecommerce_promotion_code (
    id serial NOT NULL PRIMARY KEY,
    promotion_id integer NOT NULL REFERENCES ecommerce_promotion ON UPDATE CASCADE ON DELETE RESTRICT,
    code varchar(255),
    order_id integer  NOT NULL REFERENCES ecommerce_order ON UPDATE CASCADE ON DELETE RESTRICT
);

COMMIT;
