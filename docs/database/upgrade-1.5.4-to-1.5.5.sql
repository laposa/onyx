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

/* update shipping tables to follow naming convetions */
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
