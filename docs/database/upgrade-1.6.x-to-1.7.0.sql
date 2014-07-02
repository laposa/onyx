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
DROP TABLE IF EXISTS client_acl;

CREATE TABLE client_role (
	id serial NOT NULL PRIMARY KEY,
	name varchar(255) ,
	description text ,
	other_data text
);

CREATE TABLE client_role_permission (
	id serial NOT NULL PRIMARY KEY,
	role_id integer NOT NULL REFERENCES client_role ON UPDATE CASCADE ON DELETE CASCADE,
	permission integer NOT NULL,
	scope text,
	created timestamp without time zone NOT NULL DEFAULT NOW(),
	modified timestamp without time zone NOT NULL DEFAULT NOW(),
	other_data text
);
CREATE INDEX client_role_role_id_key ON client_role_permission USING btree (role_id);
CREATE INDEX client_role_permission_key ON client_role_permission USING btree (permission);

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
-- Default ACL settings
--
INSERT INTO "client_role" ("id", "name", "description", "other_data") VALUES
(1,	'Admin',	NULL,	NULL),
(3,	'Warehouse',	NULL,	NULL),
(2,	'Editor',	NULL,	NULL);

INSERT INTO "client_role_permission" ("id", "role_id", "permission", "scope", "created", "modified", "other_data") VALUES
-- Admin
(1, 1, 1000, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_FRONT_END_EDITING
(2, 1, 2000, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_PAGES_SECTION
(3, 1, 2001, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_NEWS_SECTION
(4, 1, 2002, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_PRODUCTS_SECTION
(5, 1, 2003, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_RECIPES_SECTION
(6, 1, 2004, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_STORES_SECTION
(7, 1, 2005, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_ORDERS_SECTION
(8, 1, 2006, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_STOCK_SECTION
(9, 1, 2007, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_CUSTOMERS_SECTION
(10, 1, 2008, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_STATS_SECTION
(11, 1, 2009, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_MARKETING_SECTION
(12, 1, 2010, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_COMMENTS_SECTION
(13, 1, 2011, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_SURVEYS_SECTION
(14, 1, 2012, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_ADVANCED_SECTION
-- Editor
(15, 2, 1000, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_FRONT_END_EDITING
(16, 2, 2000, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_PAGES_SECTION
(17, 2, 2001, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_NEWS_SECTION
(18, 2, 2012, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_ADVANCED_SECTION
-- Warehouse
(19, 3, 2005, NULL, now(), now(), NULL), -- ONXSHOP_PERMISSION_ORDERS_SECTION
(20, 3, 2006, NULL, now(), now(), NULL); -- ONXSHOP_PERMISSION_STOCK_SECTION

SELECT setval('client_role_id_seq', (SELECT MAX(id) FROM client_role));
SELECT setval('client_role_permission_id_seq', (SELECT MAX(id) FROM client_role_permission));

COMMIT;
