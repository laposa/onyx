BEGIN;

CREATE TABLE ecommerce_delivery_carrier_rate (
	id serial PRIMARY KEY NOT NULL,
	carrier_id int REFERENCES ecommerce_delivery_carrier ON UPDATE CASCADE ON DELETE CASCADE,
	weight_from numeric(12,5) DEFAULT 0,
	weight_to numeric(12,5) DEFAULT 0,
	price numeric(12,5)
);

CREATE INDEX ecommerce_delivery_carrier_rate_carrier_id_idx ON ecommerce_delivery_carrier_rate USING btree (carrier_id);
CREATE INDEX ecommerce_delivery_carrier_rate_weight_idx ON ecommerce_delivery_carrier_rate USING btree (weight_from, weight_to);

COMMIT;
