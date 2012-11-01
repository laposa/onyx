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
