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

ALTER INDEX "shipping_wz_zone_price_zone_id_fkey" RENAME TO "ecommerce_delivery_carrier_zone_price_zone_id_fkey";
ALTER INDEX "shipping_wz_country_to_zone_zone_id_fkey" RENAME TO "ecommerce_delivery_carrier_country_to_zone_zone_id_fkey";
ALTER INDEX "shipping_wz_country_to_zone_country_id_fkey" RENAME TO "ecommerce_delivery_carrier_country_to_zone_country_id_fkey";

/* education_survey_image and content column for RTE */

ALTER TABLE "education_survey_question" ADD COLUMN "content" text;
ALTER TABLE "education_survey_question_answer" ADD COLUMN "content" text;

/* education_survey_entry update */

ALTER TABLE "education_survey_entry" ADD COLUMN "ip_adress" character varying(255);
ALTER TABLE "education_survey_entry" ADD COLUMN "session_id" character varying(32);

/* common_node update */

ALTER TABLE common_node ADD COLUMN share_counter int NOT NULL DEFAULT 0;

/* survey images table */

CREATE SEQUENCE education_survey_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE education_survey_image (
    id integer DEFAULT nextval('education_survey_image_id_seq'::regclass) NOT NULL PRIMARY KEY,
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

CREATE SEQUENCE ecommerce_recipe_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE ecommerce_recipe (
    id integer DEFAULT nextval('ecommerce_recipe_id_seq'::regclass) NOT NULL PRIMARY KEY,
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

CREATE SEQUENCE ecommerce_recipe_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE ecommerce_recipe_image (
    id integer DEFAULT nextval('ecommerce_recipe_image_id_seq'::regclass) NOT NULL PRIMARY KEY,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL REFERENCES ecommerce_recipe(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone DEFAULT now(),
    author integer
);

CREATE INDEX ecommerce_recipe_image_node_id_key ON ecommerce_recipe_image USING btree (node_id);

CREATE SEQUENCE ecommerce_recipe_ingredients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE ecommerce_recipe_ingredients (
    id integer DEFAULT nextval('ecommerce_recipe_ingredients_id_seq'::regclass) NOT NULL PRIMARY KEY,
    recipe_id integer REFERENCES ecommerce_recipe(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    product_variety_id integer NOT NULL REFERENCES ecommerce_product_variety(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    quantity real,
    units integer REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    notes text,
    group_title character varying(255)
);

CREATE SEQUENCE ecommerce_recipe_taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE ecommerce_recipe_taxonomy (
    id integer DEFAULT nextval('ecommerce_recipe_taxonomy_id_seq'::regclass) NOT NULL PRIMARY KEY,
    node_id integer NOT NULL REFERENCES ecommerce_recipe(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    taxonomy_tree_id integer NOT NULL REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    UNIQUE (node_id, taxonomy_tree_id)
);

CREATE INDEX ecommerce_recipe_taxonomy_node_id_key1 ON ecommerce_recipe_taxonomy USING btree (node_id);
CREATE INDEX ecommerce_recipe_taxonomy_taxonomy_tree_id_key ON ecommerce_recipe_taxonomy USING btree (taxonomy_tree_id);

/* recipes schema */

CREATE SEQUENCE ecommerce_store_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE ecommerce_store (
    id integer DEFAULT nextval('ecommerce_store_id_seq'::regclass) NOT NULL PRIMARY KEY,
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
    other_data text
);

CREATE SEQUENCE ecommerce_store_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE ecommerce_store_image (
    id integer DEFAULT nextval('ecommerce_store_image_id_seq'::regclass) NOT NULL PRIMARY KEY,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL REFERENCES ecommerce_store(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer
);

CREATE INDEX ecommerce_store_image_node_id_key ON ecommerce_store_image USING btree (node_id);

CREATE SEQUENCE ecommerce_store_taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE ecommerce_store_taxonomy (
    id integer DEFAULT nextval('ecommerce_store_taxonomy_id_seq'::regclass) NOT NULL PRIMARY KEY,
    node_id integer NOT NULL REFERENCES ecommerce_store(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    taxonomy_tree_id integer NOT NULL REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    UNIQUE (node_id, taxonomy_tree_id)
);

CREATE INDEX ecommerce_store_taxonomy_node_id_key1 ON ecommerce_store_taxonomy USING btree (node_id);
CREATE INDEX ecommerce_store_taxonomy_taxonomy_tree_id_key ON ecommerce_store_taxonomy USING btree (taxonomy_tree_id);

/* client customer upgrade */

CREATE SEQUENCE client_customer_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE client_customer_image (
    id integer DEFAULT nextval('client_customer_image_id_seq'::regclass) NOT NULL PRIMARY KEY,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL REFERENCES client_customer(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer
);

CREATE INDEX client_customer_image_node_id_key ON client_customer_image USING btree (node_id);

CREATE SEQUENCE client_customer_taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE client_customer_taxonomy (
    id integer DEFAULT nextval('client_customer_taxonomy_id_seq'::regclass) NOT NULL PRIMARY KEY,
    node_id integer NOT NULL REFERENCES client_customer(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    taxonomy_tree_id integer NOT NULL REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    UNIQUE (node_id, taxonomy_tree_id)
);

CREATE INDEX client_customer_taxonomy_node_id_key1 ON client_customer_taxonomy USING btree (node_id);
CREATE INDEX client_customer_taxonomy_taxonomy_tree_id_key ON client_customer_taxonomy USING btree (taxonomy_tree_id);


/* scheduler */

CREATE SEQUENCE common_scheduler_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE common_scheduler (
    id integer DEFAULT nextval('common_scheduler_id_seq'::regclass) NOT NULL PRIMARY KEY,
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

COMMIT;