BEGIN;

DELETE FROM ecommerce_transaction
WHERE id IN (SELECT id
              FROM (SELECT id,
                             ROW_NUMBER() OVER (partition BY order_id, pg_data ORDER BY id) AS rnum
                     FROM ecommerce_transaction) t
              WHERE t.rnum > 1);
              
ALTER TABLE ONLY ecommerce_transaction ADD CONSTRAINT ecommerce_transaction_order_id_pg_data_key UNIQUE (order_id, pg_data);

COMMIT;
