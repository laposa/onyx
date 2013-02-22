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

--
-- Name: ecommerce_invoice_order_id_idx; Type: INDEX; Schema: public; Owner: jing; Tablespace: 
--

CREATE INDEX ecommerce_invoice_order_id_idx ON ecommerce_invoice USING btree (order_id);

--
--
-- Recipes & Stores schema
--
--

--
-- Name: ecommerce_recipe_id_seq; Type: SEQUENCE; Schema: public; Owner: centra
--

CREATE SEQUENCE ecommerce_recipe_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: ecommerce_recipe; Type: TABLE; Schema: public; Owner: centra; Tablespace: 
--

CREATE TABLE ecommerce_recipe (
    id integer DEFAULT nextval('ecommerce_recipe_id_seq'::regclass) NOT NULL,
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


--
-- Name: ecommerce_recipe_image_id_seq; Type: SEQUENCE; Schema: public; Owner: centra
--

CREATE SEQUENCE ecommerce_recipe_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: ecommerce_recipe_image; Type: TABLE; Schema: public; Owner: centra; Tablespace: 
--

CREATE TABLE ecommerce_recipe_image (
    id integer DEFAULT nextval('ecommerce_recipe_image_id_seq'::regclass) NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone DEFAULT now(),
    author integer
);


--
-- Name: ecommerce_recipe_ingredients_id_seq; Type: SEQUENCE; Schema: public; Owner: centra
--

CREATE SEQUENCE ecommerce_recipe_ingredients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: ecommerce_recipe_ingredients; Type: TABLE; Schema: public; Owner: centra; Tablespace: 
--

CREATE TABLE ecommerce_recipe_ingredients (
    id integer DEFAULT nextval('ecommerce_recipe_ingredients_id_seq'::regclass) NOT NULL,
    recipe_id integer,
    product_id integer NOT NULL,
    quantity integer,
    units integer,
    notes text
);


--
-- Name: ecommerce_recipe_taxonomy_id_seq; Type: SEQUENCE; Schema: public; Owner: centra
--

CREATE SEQUENCE ecommerce_recipe_taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: ecommerce_recipe_taxonomy; Type: TABLE; Schema: public; Owner: centra; Tablespace: 
--

CREATE TABLE ecommerce_recipe_taxonomy (
    id integer DEFAULT nextval('ecommerce_recipe_taxonomy_id_seq'::regclass) NOT NULL,
    node_id integer NOT NULL,
    taxonomy_tree_id integer NOT NULL
);

--
-- Name: ecommerce_store_id_seq; Type: SEQUENCE; Schema: public; Owner: centra
--

CREATE SEQUENCE ecommerce_store_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: ecommerce_store; Type: TABLE; Schema: public; Owner: centra; Tablespace: 
--

CREATE TABLE ecommerce_store (
    id integer DEFAULT nextval('ecommerce_store_id_seq'::regclass) NOT NULL,
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


--
-- Name: ecommerce_store_image_id_seq; Type: SEQUENCE; Schema: public; Owner: centra
--

CREATE SEQUENCE ecommerce_store_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: ecommerce_store_image; Type: TABLE; Schema: public; Owner: centra; Tablespace: 
--

CREATE TABLE ecommerce_store_image (
    id integer DEFAULT nextval('ecommerce_store_image_id_seq'::regclass) NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer
);


--
-- Name: ecommerce_store_taxonomy_id_seq; Type: SEQUENCE; Schema: public; Owner: centra
--

CREATE SEQUENCE ecommerce_store_taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- Name: ecommerce_store_taxonomy; Type: TABLE; Schema: public; Owner: centra; Tablespace: 
--

CREATE TABLE ecommerce_store_taxonomy (
    id integer DEFAULT nextval('ecommerce_store_taxonomy_id_seq'::regclass) NOT NULL,
    node_id integer NOT NULL,
    taxonomy_tree_id integer NOT NULL
);

--
-- Name: ecommerce_recipe_image_pkey; Type: CONSTRAINT; Schema: public; Owner: centra; Tablespace: 
--

ALTER TABLE ONLY ecommerce_recipe_image
    ADD CONSTRAINT ecommerce_recipe_image_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_recipe_ingredients_pkey; Type: CONSTRAINT; Schema: public; Owner: centra; Tablespace: 
--

ALTER TABLE ONLY ecommerce_recipe_ingredients
    ADD CONSTRAINT ecommerce_recipe_ingredients_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_recipe_pkey; Type: CONSTRAINT; Schema: public; Owner: centra; Tablespace: 
--

ALTER TABLE ONLY ecommerce_recipe
    ADD CONSTRAINT ecommerce_recipe_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_recipe_taxonomy_node_id_key; Type: CONSTRAINT; Schema: public; Owner: centra; Tablespace: 
--

ALTER TABLE ONLY ecommerce_recipe_taxonomy
    ADD CONSTRAINT ecommerce_recipe_taxonomy_node_id_key UNIQUE (node_id, taxonomy_tree_id);


--
-- Name: ecommerce_recipe_taxonomy_pkey; Type: CONSTRAINT; Schema: public; Owner: centra; Tablespace: 
--

ALTER TABLE ONLY ecommerce_recipe_taxonomy
    ADD CONSTRAINT ecommerce_recipe_taxonomy_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_store_image_pkey; Type: CONSTRAINT; Schema: public; Owner: centra; Tablespace: 
--

ALTER TABLE ONLY ecommerce_store_image
    ADD CONSTRAINT ecommerce_store_image_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_store_pkey; Type: CONSTRAINT; Schema: public; Owner: centra; Tablespace: 
--

ALTER TABLE ONLY ecommerce_store
    ADD CONSTRAINT ecommerce_store_pkey PRIMARY KEY (id);


--
-- Name: ecommerce_store_taxonomy_node_id_key; Type: CONSTRAINT; Schema: public; Owner: centra; Tablespace: 
--

ALTER TABLE ONLY ecommerce_store_taxonomy
    ADD CONSTRAINT ecommerce_store_taxonomy_node_id_key UNIQUE (node_id, taxonomy_tree_id);


--
-- Name: ecommerce_store_taxonomy_pkey; Type: CONSTRAINT; Schema: public; Owner: centra; Tablespace: 
--

ALTER TABLE ONLY ecommerce_store_taxonomy
    ADD CONSTRAINT ecommerce_store_taxonomy_pkey PRIMARY KEY (id);

--
-- Name: ecommerce_recipe_image_node_id_key; Type: INDEX; Schema: public; Owner: centra; Tablespace: 
--

CREATE INDEX ecommerce_recipe_image_node_id_key ON ecommerce_recipe_image USING btree (node_id);


--
-- Name: ecommerce_recipe_taxonomy_node_id_key1; Type: INDEX; Schema: public; Owner: centra; Tablespace: 
--

CREATE INDEX ecommerce_recipe_taxonomy_node_id_key1 ON ecommerce_recipe_taxonomy USING btree (node_id);


--
-- Name: ecommerce_recipe_taxonomy_taxonomy_tree_id_key; Type: INDEX; Schema: public; Owner: centra; Tablespace: 
--

CREATE INDEX ecommerce_recipe_taxonomy_taxonomy_tree_id_key ON ecommerce_recipe_taxonomy USING btree (taxonomy_tree_id);


--
-- Name: ecommerce_store_image_node_id_key; Type: INDEX; Schema: public; Owner: centra; Tablespace: 
--

CREATE INDEX ecommerce_store_image_node_id_key ON ecommerce_store_image USING btree (node_id);


--
-- Name: ecommerce_store_taxonomy_node_id_key1; Type: INDEX; Schema: public; Owner: centra; Tablespace: 
--

CREATE INDEX ecommerce_store_taxonomy_node_id_key1 ON ecommerce_store_taxonomy USING btree (node_id);


--
-- Name: ecommerce_store_taxonomy_taxonomy_tree_id_key; Type: INDEX; Schema: public; Owner: centra; Tablespace: 
--

CREATE INDEX ecommerce_store_taxonomy_taxonomy_tree_id_key ON ecommerce_store_taxonomy USING btree (taxonomy_tree_id);


--
-- Name: ecommerce_recipe_ingredients_product_fkey; Type: FK CONSTRAINT; Schema: public; Owner: centra
--

ALTER TABLE ONLY ecommerce_recipe_ingredients
    ADD CONSTRAINT ecommerce_recipe_ingredients_product_fkey FOREIGN KEY (product_id) REFERENCES ecommerce_product(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_ingredients_recipe_fkey; Type: FK CONSTRAINT; Schema: public; Owner: centra
--

ALTER TABLE ONLY ecommerce_recipe_ingredients
    ADD CONSTRAINT ecommerce_recipe_ingredients_recipe_fkey FOREIGN KEY (recipe_id) REFERENCES ecommerce_recipe(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_ingredients_units_fkey; Type: FK CONSTRAINT; Schema: public; Owner: centra
--

ALTER TABLE ONLY ecommerce_recipe_ingredients
    ADD CONSTRAINT ecommerce_recipe_ingredients_units_fkey FOREIGN KEY (units) REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_node_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: centra
--

ALTER TABLE ONLY ecommerce_recipe_image
    ADD CONSTRAINT ecommerce_recipe_node_id_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_recipe(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_taxonomy_recipe_fkey; Type: FK CONSTRAINT; Schema: public; Owner: centra
--

ALTER TABLE ONLY ecommerce_recipe_taxonomy
    ADD CONSTRAINT ecommerce_recipe_taxonomy_recipe_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_recipe(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_recipe_taxonomy_taxonmy_fkey; Type: FK CONSTRAINT; Schema: public; Owner: centra
--

ALTER TABLE ONLY ecommerce_recipe_taxonomy
    ADD CONSTRAINT ecommerce_recipe_taxonomy_taxonmy_fkey FOREIGN KEY (taxonomy_tree_id) REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_store_image_store_fkey; Type: FK CONSTRAINT; Schema: public; Owner: centra
--

ALTER TABLE ONLY ecommerce_store_image
    ADD CONSTRAINT ecommerce_store_image_store_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_store(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_store_taxonomy_store_fkey; Type: FK CONSTRAINT; Schema: public; Owner: centra
--

ALTER TABLE ONLY ecommerce_store_taxonomy
    ADD CONSTRAINT ecommerce_store_taxonomy_store_fkey FOREIGN KEY (node_id) REFERENCES ecommerce_store(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: ecommerce_store_taxonomy_taxonomy_fkey; Type: FK CONSTRAINT; Schema: public; Owner: centra
--

ALTER TABLE ONLY ecommerce_store_taxonomy
    ADD CONSTRAINT ecommerce_store_taxonomy_taxonomy_fkey FOREIGN KEY (taxonomy_tree_id) REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
--
-- Client customer upgrade
--
--

--
-- Name: client_customer_image_id_seq; Type: SEQUENCE; Schema: public; Owner: centra
--

CREATE SEQUENCE client_customer_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.client_customer_image_id_seq OWNER TO centra;

--
-- Name: client_customer_image; Type: TABLE; Schema: public; Owner: centra; Tablespace: 
--

CREATE TABLE client_customer_image (
    id integer DEFAULT nextval('client_customer_image_id_seq'::regclass) NOT NULL,
    src character varying(255),
    role character varying(255),
    node_id integer NOT NULL,
    title character varying(255),
    description text,
    priority integer DEFAULT 0 NOT NULL,
    modified timestamp(0) without time zone,
    author integer
);

--
-- Name: client_customer_image_pkey; Type: CONSTRAINT; Schema: public; Owner: centra; Tablespace: 
--

ALTER TABLE ONLY client_customer_image
    ADD CONSTRAINT client_customer_image_pkey PRIMARY KEY (id);

--
-- Name: client_customer_image_node_id_key; Type: INDEX; Schema: public; Owner: centra; Tablespace: 
--

CREATE INDEX client_customer_image_node_id_key ON client_customer_image USING btree (node_id);

--
-- Name: client_customer_image_customer_fkey; Type: FK CONSTRAINT; Schema: public; Owner: centra
--

ALTER TABLE ONLY client_customer_image
    ADD CONSTRAINT client_customer_image_customer_fkey FOREIGN KEY (node_id) REFERENCES client_customer(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: client_customer_taxonomy_id_seq; Type: SEQUENCE; Schema: public; Owner: centra
--

CREATE SEQUENCE client_customer_taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.client_customer_taxonomy_id_seq OWNER TO centra;

--
-- Name: client_customer_taxonomy; Type: TABLE; Schema: public; Owner: centra; Tablespace: 
--

CREATE TABLE client_customer_taxonomy (
    id integer DEFAULT nextval('client_customer_taxonomy_id_seq'::regclass) NOT NULL,
    node_id integer NOT NULL,
    taxonomy_tree_id integer NOT NULL
);

--
-- Name: client_customer_taxonomy_node_id_key; Type: CONSTRAINT; Schema: public; Owner: centra; Tablespace: 
--

ALTER TABLE ONLY client_customer_taxonomy
    ADD CONSTRAINT client_customer_taxonomy_node_id_key UNIQUE (node_id, taxonomy_tree_id);


--
-- Name: client_customer_taxonomy_pkey; Type: CONSTRAINT; Schema: public; Owner: centra; Tablespace: 
--

ALTER TABLE ONLY client_customer_taxonomy
    ADD CONSTRAINT client_customer_taxonomy_pkey PRIMARY KEY (id);


--
-- Name: client_customer_taxonomy_node_id_key1; Type: INDEX; Schema: public; Owner: centra; Tablespace: 
--

CREATE INDEX client_customer_taxonomy_node_id_key1 ON client_customer_taxonomy USING btree (node_id);


--
-- Name: client_customer_taxonomy_taxonomy_tree_id_key; Type: INDEX; Schema: public; Owner: centra; Tablespace: 
--

CREATE INDEX client_customer_taxonomy_taxonomy_tree_id_key ON client_customer_taxonomy USING btree (taxonomy_tree_id);

--
-- Name: client_customer_taxonomy_customer_fkey; Type: FK CONSTRAINT; Schema: public; Owner: centra
--

ALTER TABLE ONLY client_customer_taxonomy
    ADD CONSTRAINT client_customer_taxonomy_customer_fkey FOREIGN KEY (node_id) REFERENCES client_customer(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: client_customer_taxonomy_taxonomy_fkey; Type: FK CONSTRAINT; Schema: public; Owner: centra
--

ALTER TABLE ONLY client_customer_taxonomy
    ADD CONSTRAINT client_customer_taxonomy_taxonomy_fkey FOREIGN KEY (taxonomy_tree_id) REFERENCES common_taxonomy_tree(id) ON UPDATE RESTRICT ON DELETE RESTRICT;

COMMIT;