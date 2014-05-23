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
ADD COLUMN address_post_code varchar(255)

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

--
-- send review notification 14 days after order
--  
ALTER TABLE ecommerce_order ADD COLUMN review_email_sent integer;
CREATE INDEX ecommerce_order_review_email_sent_idx ON ecommerce_order USING btree (review_email_sent);
UPDATE ecommerce_order SET review_email_sent = 1;

COMMIT;
