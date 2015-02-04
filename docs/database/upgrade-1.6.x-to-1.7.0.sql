BEGIN;

--
-- special offers
--

CREATE TABLE ecommerce_offer_group (
    id serial NOT NULL PRIMARY KEY,
    title character varying(255),
    description text,
    schedule_start timestamp(0) without time zone,
    schedule_end timestamp(0) without time zone,
    publish integer DEFAULT 0 NOT NULL,
    created timestamp(0) without time zone,
    modified timestamp(0) without time zone,
    other_data text
);

ALTER TABLE ecommerce_offer 
ADD COLUMN offer_group_id integer REFERENCES ecommerce_offer_group ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE ecommerce_offer 
ADD COLUMN priority integer DEFAULT 0 NOT NULL;

--
-- watchdog
--

CREATE TABLE common_watchdog (
	id serial PRIMARY KEY NOT NULL,
	name character varying(255),
	watched_item_id integer,
	customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
	created timestamp without time zone,
	modified timestamp without time zone DEFAULT now(),
	publish smallint,
	other_data text
);

CREATE INDEX common_watchdog_combined_idx ON common_watchdog USING btree (name, watched_item_id, publish);

--
-- customer
--

ALTER TABLE client_customer
ADD COLUMN store_id integer REFERENCES ecommerce_store ON UPDATE CASCADE ON DELETE RESTRICT;

--
-- store
--

ALTER TABLE ecommerce_store
ADD COLUMN country_id int REFERENCES international_country ON UPDATE CASCADE ON DELETE RESTRICT,
ADD COLUMN address_name varchar(255),
ADD COLUMN address_line_1 varchar(255),
ADD COLUMN address_line_2 varchar(255),
ADD COLUMN address_line_3 varchar(255),
ADD COLUMN address_city varchar(255),
ADD COLUMN address_county varchar(255),
ADD COLUMN address_post_code varchar(255);

--
-- customer_id to core nodes
--	
ALTER TABLE common_node ADD COLUMN customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE common_file ADD COLUMN customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE common_image ADD COLUMN customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE ecommerce_product_image ADD COLUMN customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE ecommerce_store_image ADD COLUMN customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE ecommerce_product_variety_image ADD COLUMN customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE ecommerce_recipe_image ADD COLUMN customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE common_taxonomy_label_image ADD COLUMN customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE client_customer_image ADD COLUMN customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT;
ALTER TABLE education_survey_image ADD COLUMN customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT;

--
-- send review notification 14 days after order
--  
ALTER TABLE ecommerce_order ADD COLUMN review_email_sent integer;
CREATE INDEX ecommerce_order_review_email_sent_idx ON ecommerce_order USING btree (review_email_sent);
UPDATE ecommerce_order SET review_email_sent = 1;

--
-- Make customer_group M:N
--
CREATE TABLE client_customer_group (
	id serial NOT NULL PRIMARY KEY,
	group_id integer NOT NULL REFERENCES client_group ON UPDATE CASCADE ON DELETE CASCADE,
	customer_id integer NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE,
	created timestamp without time zone NOT NULL DEFAULT NOW(),
	modified timestamp without time zone NOT NULL DEFAULT NOW()
);
CREATE INDEX client_customer_group_group_id_key ON client_customer_group USING btree (group_id);
CREATE INDEX client_customer_group_customer_id_key ON client_customer_group USING btree (customer_id);

--
-- insert old data from client_customer.group_id to client_customer_group
--
INSERT INTO client_customer_group (group_id, customer_id, created, modified)
SELECT group_id, id, now(), now() FROM client_customer WHERE group_id > 0;

--
-- remove deprecated column
--
ALTER TABLE client_customer DROP COLUMN group_id;

--
-- ACL
--  

CREATE TYPE acl_resource AS ENUM (
	'_all_',
	'front_office',
	'back_office',
	'nodes',
	'products',
	'recipes',
	'stores',
	'orders',
	'stock',
	'customers',
	'reports',
	'discounts',
	'comments',
	'surveys',
	'media',
	'taxonomy',
	'seo_manager',
	'database',
	'templates',
	'scheduler',
	'currency',
	'search_index',
	'tools',
	'logs',
	'configuration',
	'permissions'
);

CREATE TYPE acl_operation AS ENUM (
	'_all_',
	'view',
	'edit',
	'add',
	'delete',
	'publish'
);

--
-- client_role
--

CREATE TABLE client_role (
	id serial NOT NULL PRIMARY KEY,
	name varchar(255) ,
	description text ,
	other_data text
);

INSERT INTO "client_role" ("id", "name", "description", "other_data") VALUES
(1, 'Admin', NULL, NULL),
(2, 'Front Office Only CMS Editor', NULL, NULL),
(3, 'CMS Editor', NULL, NULL),
(4, 'eCommerce Editor', NULL, NULL),
(5, 'Customer Services', NULL, NULL),
(6, 'Warehouse', NULL, NULL);
SELECT setval('client_role_id_seq', 1000); -- role ID under 1000 is reserved


--
-- client_role_permission
--

CREATE TABLE client_role_permission (
	id serial NOT NULL PRIMARY KEY,
	role_id integer NOT NULL REFERENCES client_role ON UPDATE CASCADE ON DELETE CASCADE,
	resource acl_resource,
	operation acl_operation,
	scope text,
	created timestamp without time zone NOT NULL DEFAULT NOW(),
	modified timestamp without time zone NOT NULL DEFAULT NOW(),
	other_data text
);
CREATE INDEX client_role_permission_role_id_key ON client_role_permission USING btree (role_id);


INSERT INTO "client_role_permission" ("role_id", "resource", "operation") VALUES
--
-- Admin
--
(1, '_all_', '_all_'),
--
-- Front Office Only CMS Editor
--
(2, 'front_office', '_all_'),
(2, 'nodes', 'edit'),
(2, 'media', '_all_'),
--
-- CMS Editor
--
(3, 'front_office', '_all_'),
(3, 'back_office', '_all_'),
(3, 'nodes', '_all_'),
(3, 'comments', '_all_'),
(3, 'surveys', '_all_'),
(3, 'media', '_all_'),
(3, 'taxonomy', '_all_'),
(3, 'seo_manager', '_all_'),
(3, 'scheduler', '_all_'),
(3, 'search_index', '_all_'),
--
-- Ecommerce Editor
--
(4, 'products', '_all_'),
(4, 'recipes', '_all_'),
(4, 'stores', '_all_'),
(4, 'orders', '_all_'),
(4, 'stock', '_all_'),
(4, 'customers', '_all_'),
(4, 'reports', '_all_'),
(4, 'discounts', '_all_'),
(4, 'currency', '_all_'),
--
-- Customer Services
--
(5, 'back_office', '_all_'),
(5, 'customers', '_all_'),
(5, 'orders', '_all_'),
(5, 'comments', '_all_'),
(5, 'surveys', '_all_'),
(5, 'discounts', '_all_'),
--
-- Warehouse
--
(6, 'stock', '_all_');

SELECT setval('client_role_permission_id_seq', (SELECT MAX(id) FROM client_role_permission));


--
-- client_customer_role
--

CREATE TABLE client_customer_role (
	id serial NOT NULL PRIMARY KEY,
	role_id integer NOT NULL REFERENCES client_role ON UPDATE CASCADE ON DELETE CASCADE,
	customer_id integer NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE,
	created timestamp without time zone NOT NULL DEFAULT NOW(),
	modified timestamp without time zone NOT NULL DEFAULT NOW()
);
CREATE INDEX client_customer_role_role_id_key ON client_customer_role USING btree (role_id);
CREATE INDEX client_customer_role_customer_id_key ON client_customer_role USING btree (customer_id);




--
-- Add other_data column to some education_* tables
--
ALTER TABLE education_survey ADD COLUMN other_data text;
ALTER TABLE education_survey_question ADD COLUMN other_data text;
ALTER TABLE education_survey_question_answer ADD COLUMN other_data text;

--
-- Add code column to some ecommerce_store
--
ALTER TABLE ecommerce_store ADD COLUMN code varchar(255);

--
-- Add new column common_node.apply_to_children (default 0)
--

ALTER TABLE "common_configuration" ADD "apply_to_children" smallint NULL DEFAULT '0';

--
-- Add new column ecommerce_promotion.limit_cumulative_discount
--

ALTER TABLE "ecommerce_promotion" ADD "limit_cumulative_discount" numeric(12,5) NULL DEFAULT 0;

--
-- Add new column ecommerce_promotion.free_promo_products
--

ALTER TABLE "ecommerce_promotion" ADD "free_promo_products" text NULL;

--
-- Add new column ecommerce_store.url
--

ALTER TABLE "ecommerce_store" ADD "url" varchar(512) NULL;



--
-- Revisions
--

CREATE TABLE common_revision (
	id serial PRIMARY KEY NOT NULL,
	object varchar(255) NOT NULL,
	node_id integer NOT NULL,
	content text,
	status smallint,
	customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
	created timestamp without time zone NOT NULL DEFAULT NOW(),
	modified timestamp without time zone NOT NULL DEFAULT NOW(),
	other_data text
);
		
CREATE INDEX common_revision_combined_idx
	ON common_revision
	USING btree
	(object, node_id);


--
-- Add new column ecommerce_product_variety
--

ALTER TABLE "ecommerce_product_variety" ADD "limit_to_delivery_zones" varchar(512) NULL;



--
-- international_translation
--

CREATE TABLE international_translation (
	id serial NOT NULL PRIMARY KEY,
	locale character varying(20) NOT NULL,
	original_string text NOT NULL,
	translated_string text NOT NULL,
	context character varying(63),
	node_id integer REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE INDEX international_translation_locale_idx ON international_translation USING btree (locale);
CREATE INDEX international_translation_node_id_idx ON international_translation USING btree (node_id);

--
-- add janrain to customer
--

ALTER TABLE client_customer ADD COLUMN janrain_id character varying(255);

--
-- create bin
--

INSERT INTO common_node VALUES (94, 'Bin', 'page', 'default', 0, 0, 0, '', NULL, '', '', '', '', '2014-12-07 00:00:00', '2014-12-07 00:00:00', 0, 0, 1000, '', 0, NULL, '', '', NULL, NULL, 1, 0, 0, 0, '', 0, 0, '', 0);
INSERT INTO common_uri_mapping (node_id, public_uri, type) VALUES (94, '/bin', 'generic');

COMMIT;
