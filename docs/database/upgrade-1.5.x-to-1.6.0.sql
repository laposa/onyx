BEGIN;

/* voucher author */

ALTER TABLE "ecommerce_promotion" ADD COLUMN "generated_by_customer_id" integer
REFERENCES "client_customer" ON UPDATE CASCADE ON DELETE RESTRICT;

/* voucher limited to specific customer (reward for inviting) */

ALTER TABLE "ecommerce_promotion" ADD COLUMN "limit_by_customer_id" integer DEFAULT 0
REFERENCES "client_customer" ON UPDATE CASCADE ON DELETE RESTRICT;

/* voucher limited to first order */

ALTER TABLE "ecommerce_promotion" ADD COLUMN "limit_to_first_order" smallint NOT NULL DEFAULT 0;

/* voucher limited to minim order amount */

ALTER TABLE "ecommerce_promotion" ADD COLUMN "limit_to_order_amount" numeric(12,5) DEFAULT 0;

/* update shipping tables to follow naming conventions */

ALTER SEQUENCE "shipping_wz_zone_id_seq" RENAME TO "ecommerce_delivery_carrier_zone_id_seq";
ALTER SEQUENCE "shipping_wz_zone_price_id_seq" RENAME TO "ecommerce_delivery_carrier_zone_price_id_seq";
ALTER SEQUENCE "shipping_wz_country_to_zone_id_seq" RENAME TO "ecommerce_delivery_carrier_zone_to_country_id_seq";

ALTER INDEX "shipping_wz_zone_pkey" RENAME TO "ecommerce_delivery_carrier_zone_pkey";
ALTER INDEX "shipping_wz_zone_price_pkey" RENAME TO "ecommerce_delivery_carrier_zone_price_pkey";
ALTER INDEX "shipping_wz_country_to_zone_pkey" RENAME TO "ecommerce_delivery_carrier_zone_to_country_pkey";

SELECT pg_catalog.setval('ecommerce_delivery_carrier_zone_id_seq', (SELECT max(id) FROM ecommerce_delivery_carrier_zone), true);
SELECT pg_catalog.setval('ecommerce_delivery_carrier_zone_to_country_id_seq', (SELECT max(id) FROM ecommerce_delivery_carrier_zone_to_country), true);
SELECT pg_catalog.setval('ecommerce_delivery_carrier_zone_price_id_seq', (SELECT max(id) FROM ecommerce_delivery_carrier_zone_price), true);

/* education_survey_image and content column for RTE */

ALTER TABLE "education_survey_question" ADD COLUMN "content" text;
ALTER TABLE "education_survey_question_answer" ADD COLUMN "content" text;

/* education_survey_entry update */

ALTER TABLE "education_survey_entry" ADD COLUMN "ip_address" character varying(255);
ALTER TABLE "education_survey_entry" ADD COLUMN "session_id" character varying(32);
ALTER TABLE "education_survey_entry" ADD COLUMN "other_data" text;

/* survey images table */

CREATE TABLE education_survey_image (
    id serial PRIMARY KEY NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL REFERENCES education_survey(id) ON UPDATE CASCADE ON DELETE CASCADE,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer
);

CREATE INDEX education_survey_image_node_id_key ON education_survey_image USING btree (node_id);
CREATE INDEX ecommerce_invoice_order_id_idx ON ecommerce_invoice USING btree (order_id);

/* recipes schema */

CREATE TABLE ecommerce_recipe (
    id serial PRIMARY KEY NOT NULL,
    title character varying(255),
    description text,
    instructions text,
    video_url text,
    serving_people integer,
    preparation_time integer,
    cooking_time integer,
    priority integer,
    created timestamp without time zone,
    modified timestamp without time zone DEFAULT now(),
    publish smallint,
    other_data text
);

CREATE TABLE ecommerce_recipe_image (
    id serial PRIMARY KEY NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL REFERENCES ecommerce_recipe(id) ON UPDATE CASCADE ON DELETE CASCADE,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone DEFAULT now(),
    author integer
);

CREATE INDEX ecommerce_recipe_image_node_id_key ON ecommerce_recipe_image USING btree (node_id);

CREATE TABLE ecommerce_recipe_ingredients (
    id serial PRIMARY KEY NOT NULL,
    recipe_id integer REFERENCES ecommerce_recipe(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    product_variety_id integer NOT NULL REFERENCES ecommerce_product_variety(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    quantity real,
    units integer REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    notes text,
    group_title character varying(255)
);

CREATE TABLE ecommerce_recipe_taxonomy (
    id serial PRIMARY KEY NOT NULL,
    node_id integer NOT NULL REFERENCES ecommerce_recipe(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    taxonomy_tree_id integer NOT NULL REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    UNIQUE (node_id, taxonomy_tree_id)
);

CREATE TABLE ecommerce_recipe_review (
	id serial PRIMARY KEY NOT NULL,
	parent int REFERENCES ecommerce_recipe_review ON UPDATE CASCADE ON DELETE CASCADE,
	node_id int REFERENCES ecommerce_recipe ON UPDATE CASCADE ON DELETE RESTRICT,
	title varchar(255),
	content text,
	author_name varchar(255),
	author_email varchar(255),
	author_website varchar(255),
	author_ip_address varchar(255),
	customer_id int NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
	created timestamp(0) default now(),
	publish smallint,
	rating smallint default 0,
	relation_subject text
);

CREATE INDEX ecommerce_recipe_taxonomy_node_id_key1 ON ecommerce_recipe_taxonomy USING btree (node_id);
CREATE INDEX ecommerce_recipe_taxonomy_taxonomy_tree_id_key ON ecommerce_recipe_taxonomy USING btree (taxonomy_tree_id);
CREATE INDEX ecommerce_recipe_review_node_id_key1 ON ecommerce_recipe_review USING btree (node_id);

/*add missing indexes*/
CREATE INDEX ecommerce_product_review_node_id_key1 ON ecommerce_product_review USING btree (node_id);
CREATE INDEX common_comment_node_id_key1 ON common_comment USING btree (node_id);

/* recipes schema */

CREATE TABLE ecommerce_store (
    id serial PRIMARY KEY NOT NULL,
    title character varying(255),
    description text,
    address text,
    opening_hours text,
    telephone character varying(255),
    manager_name character varying(255),
    email character varying(255),
    type integer,
    coordinates_x integer,
    coordinates_y integer,
    latitude double precision,
    longitude double precision,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    publish smallint DEFAULT 0 NOT NULL,
    street_view_options text,
    other_data text
);

CREATE TABLE ecommerce_store_image (
    id serial PRIMARY KEY NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL REFERENCES ecommerce_store(id) ON UPDATE CASCADE ON DELETE CASCADE,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer
);

CREATE INDEX ecommerce_store_image_node_id_key ON ecommerce_store_image USING btree (node_id);

CREATE TABLE ecommerce_store_taxonomy (
    id serial PRIMARY KEY NOT NULL,
    node_id integer NOT NULL REFERENCES ecommerce_store(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    taxonomy_tree_id integer NOT NULL REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    UNIQUE (node_id, taxonomy_tree_id)
);

CREATE INDEX ecommerce_store_taxonomy_node_id_key1 ON ecommerce_store_taxonomy USING btree (node_id);
CREATE INDEX ecommerce_store_taxonomy_taxonomy_tree_id_key ON ecommerce_store_taxonomy USING btree (taxonomy_tree_id);

/* client customer upgrade */

CREATE TABLE client_customer_image (
    id serial PRIMARY KEY NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL REFERENCES client_customer(id) ON UPDATE CASCADE ON DELETE CASCADE,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer
);

CREATE INDEX client_customer_image_node_id_key ON client_customer_image USING btree (node_id);

CREATE TABLE client_customer_taxonomy (
    id serial PRIMARY KEY NOT NULL,
    node_id integer NOT NULL REFERENCES client_customer(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    taxonomy_tree_id integer NOT NULL REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    UNIQUE (node_id, taxonomy_tree_id)
);

CREATE INDEX client_customer_taxonomy_node_id_key1 ON client_customer_taxonomy USING btree (node_id);
CREATE INDEX client_customer_taxonomy_taxonomy_tree_id_key ON client_customer_taxonomy USING btree (taxonomy_tree_id);


/* scheduler */

CREATE TABLE common_scheduler (
    id serial PRIMARY KEY NOT NULL,
    node_id integer,
    node_type character varying(255),
    controller character varying(255),
    parameters text,
    scheduled_time timestamp without time zone,
    status smallint,
    lock_token int,
    result text,
    start_time timestamp without time zone,
    completed_time timestamp without time zone,
    created timestamp without time zone,
    modified timestamp without time zone DEFAULT now()
);

CREATE INDEX common_scheduler_node_id_key ON common_scheduler USING btree (node_id);
CREATE INDEX common_scheduler_scheduled_time_key ON common_scheduler USING btree (scheduled_time);
CREATE INDEX common_scheduler_lock_token_key ON common_scheduler USING btree (lock_token);
CREATE INDEX common_scheduler_status_key ON common_scheduler USING btree (status);


/* common_node */
ALTER TABLE common_node ADD COLUMN share_counter int NOT NULL DEFAULT 0;

/* client_customer */
ALTER TABLE client_customer ADD COLUMN oauth text;
ALTER TABLE client_customer ADD COLUMN deleted_date timestamp without time zone;
ALTER TABLE client_customer ADD UNIQUE (email, deleted_date);
ALTER TABLE client_customer ADD COLUMN facebook_id bigint;
ALTER TABLE client_customer ADD COLUMN twitter_id bigint;
ALTER TABLE client_customer ADD COLUMN google_id bigint;
ALTER TABLE client_customer ADD COLUMN profile_image_url text;

/* add column content (RTE) to all image tables */
ALTER TABLE common_file ADD COLUMN content text;
ALTER TABLE common_image ADD COLUMN content text;
ALTER TABLE common_taxonomy_label_image ADD COLUMN content text;
ALTER TABLE client_customer_image ADD COLUMN content text;
ALTER TABLE ecommerce_product_image ADD COLUMN content text;
ALTER TABLE ecommerce_product_variety_image ADD COLUMN content text;
ALTER TABLE ecommerce_recipe_image ADD COLUMN content text;
ALTER TABLE ecommerce_store_image ADD COLUMN content text;
ALTER TABLE education_survey_image ADD COLUMN content text;

/* add column other_data to all image tables */
ALTER TABLE common_file ADD COLUMN other_data text;
ALTER TABLE common_image ADD COLUMN other_data text;
ALTER TABLE common_taxonomy_label_image ADD COLUMN other_data text;
ALTER TABLE client_customer_image ADD COLUMN other_data text;
ALTER TABLE ecommerce_product_image ADD COLUMN other_data text;
ALTER TABLE ecommerce_product_variety_image ADD COLUMN other_data text;
ALTER TABLE ecommerce_recipe_image ADD COLUMN other_data text;
ALTER TABLE ecommerce_store_image ADD COLUMN other_data text;
ALTER TABLE education_survey_image ADD COLUMN other_data text;

/* add column link_to_node_id to all image tables */
ALTER TABLE common_file ADD COLUMN link_to_node_id integer;
ALTER TABLE common_image ADD COLUMN link_to_node_id integer;
ALTER TABLE common_taxonomy_label_image ADD COLUMN link_to_node_id integer;
ALTER TABLE client_customer_image ADD COLUMN link_to_node_id integer;
ALTER TABLE ecommerce_product_image ADD COLUMN link_to_node_id integer;
ALTER TABLE ecommerce_product_variety_image ADD COLUMN link_to_node_id integer;
ALTER TABLE ecommerce_recipe_image ADD COLUMN link_to_node_id integer;
ALTER TABLE ecommerce_store_image ADD COLUMN link_to_node_id integer;
ALTER TABLE education_survey_image ADD COLUMN link_to_node_id integer;

/* add extra columns to ecommerce_basket */
ALTER TABLE ecommerce_basket ADD COLUMN title character varying(255);
ALTER TABLE ecommerce_basket ADD COLUMN other_data text;

/* change client/user_prefs to client/edit (only on default installations)*/
UPDATE common_node SET component = 'a:3:{s:8:"template";s:16:"client/edit.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}' WHERE component = 'a:3:{s:8:"template";s:22:"client/user_prefs.html";s:10:"controller";s:0:"";s:9:"parameter";s:0:"";}' AND id = 54;

/* create token table */
CREATE TABLE client_customer_token (
    id serial NOT NULL PRIMARY KEY,
    customer_id integer NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE,
    publish smallint DEFAULT 0 NOT NULL,
    token character(32),
    oauth_data text,
    other_data text,
    ttl integer,
    ip_address varchar(255),
    http_user_agent varchar(255),
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL
);

CREATE INDEX client_customer_token_key ON client_customer_token USING btree (token);
CREATE INDEX client_customer_token_publish_key ON client_customer_token USING btree (publish);

/* add publish to international_country */
ALTER TABLE international_country ADD COLUMN publish smallint;
UPDATE international_country SET publish = 1;

/* create client action table */
CREATE TABLE client_action (
    id serial NOT NULL PRIMARY KEY,
    customer_id integer NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE,
    node_id integer NOT NULL REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
    action_id varchar(255),
    network varchar(255),
    action_name varchar(255),
    object_name varchar(255),
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    other_data text
);

CREATE INDEX client_action_customer_id_key ON client_action USING btree (customer_id);
CREATE INDEX client_action_network_key ON client_action USING btree (network);

/* move product_type_id from product to product_variety */
ALTER TABLE ecommerce_product_variety 
ADD COLUMN product_type_id integer 
REFERENCES ecommerce_product_type ON UPDATE CASCADE ON DELETE CASCADE;

UPDATE ecommerce_product_variety 
SET product_type_id = (
    SELECT product_type_id
    FROM ecommerce_product 
    WHERE id = ecommerce_product_variety.product_id
);

ALTER TABLE ecommerce_product DROP COLUMN product_type_id;

/* add missing node_group index on common_node */
CREATE INDEX common_node_node_controller_idx ON common_node USING btree (node_controller);

/* discount resolution for basket */

ALTER TABLE ecommerce_basket RENAME COLUMN discount_net TO face_value_voucher;
ALTER TABLE ecommerce_basket_content ADD COLUMN discount numeric(12,5) DEFAULT 0 NOT NULL;

/* discount resolution - extra table */

CREATE TABLE ecommerce_promotion_type (
    id serial NOT NULL PRIMARY KEY,
    title character varying(255),
    description text,
    taxable smallint DEFAULT 0 NOT NULL,
    publish smallint DEFAULT 1 NOT NULL,
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
    other_data text
);

INSERT INTO ecommerce_promotion_type VALUES (1, 'Discount Coupon', '', 0, 1, now(), now(), '');
INSERT INTO ecommerce_promotion_type VALUES (2, 'Referral Invite Coupon', '', 0, 1, now(), now(), '');
INSERT INTO ecommerce_promotion_type VALUES (3, 'Referral Reward Coupon', '', 0, 1, now(), now(), '');
INSERT INTO ecommerce_promotion_type VALUES (4, 'Gift Voucher', '', 1, 1, now(), now(), '');

ALTER TABLE ecommerce_promotion ADD COLUMN type integer 
REFERENCES ecommerce_promotion_type ON UPDATE CASCADE ON DELETE RESTRICT;

UPDATE ecommerce_promotion SET type = 1;
UPDATE ecommerce_promotion SET type = 2 WHERE code_pattern LIKE 'REF-%';
UPDATE ecommerce_promotion SET type = 3 WHERE code_pattern LIKE 'REW-%';
UPDATE ecommerce_promotion SET type = 4 WHERE code_pattern LIKE 'GIFT-%';

/* discount resultion for invoice */

ALTER TABLE ecommerce_invoice RENAME COLUMN voucher_discount TO face_value_voucher;

/* more invoice table changes */

ALTER TABLE ecommerce_invoice RENAME COLUMN goods_vat_sr TO goods_vat;
ALTER TABLE ecommerce_invoice DROP COLUMN goods_vat_rr;
ALTER TABLE ecommerce_invoice ADD COLUMN basket_detail_enhanced text;

COMMIT;

/*this only applies to installation made earlier than Onxshop 1.5 */
BEGIN;
ALTER INDEX "shipping_wz_zone_price_zone_id_fkey" RENAME TO "ecommerce_delivery_carrier_zone_price_zone_id_fkey";
ALTER INDEX "shipping_wz_country_to_zone_zone_id_fkey" RENAME TO "ecommerce_delivery_carrier_country_to_zone_zone_id_fkey";
ALTER INDEX "shipping_wz_country_to_zone_country_id_fkey" RENAME TO "ecommerce_delivery_carrier_country_to_zone_country_id_fkey";
COMMIT;
