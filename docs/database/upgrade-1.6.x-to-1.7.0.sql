BEGIN;

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

COMMIT;
