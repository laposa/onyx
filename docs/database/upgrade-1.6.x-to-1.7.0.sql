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

COMMIT;
