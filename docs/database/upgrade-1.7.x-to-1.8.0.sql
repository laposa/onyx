BEGIN;

ALTER TABLE client_customer ALTER facebook_id TYPE character varying(255);
ALTER TABLE client_customer ALTER twitter_id TYPE character varying(255);
ALTER TABLE client_customer ALTER google_id TYPE character varying(255);

ALTER TABLE common_node ADD COLUMN custom_fields jsonb;

CREATE INDEX common_node_custom_fields_idx ON common_node USING gin (custom_fields);

UPDATE common_node SET node_controller = lower(node_controller);

UPDATE common_node SET node_controller = 'image_gallery' WHERE node_controller = 'picture' AND node_group='content';

ALTER TABLE common_node RENAME COLUMN teaser TO strapline;

DELETE FROM ecommerce_transaction
WHERE id IN (SELECT id
              FROM (SELECT id,
                             ROW_NUMBER() OVER (partition BY order_id, pg_data ORDER BY id) AS rnum
                     FROM ecommerce_transaction) t
              WHERE t.rnum > 1);

ALTER TABLE ONLY ecommerce_transaction ADD CONSTRAINT ecommerce_transaction_order_id_pg_data_key UNIQUE (order_id, pg_data);

COMMIT;
