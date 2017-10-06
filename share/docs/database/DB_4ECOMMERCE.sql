SET client_encoding = 'UNICODE';
BEGIN;
/*ecommerce_product_type*/
INSERT INTO ecommerce_product_type VALUES (1, 'Hardware', 17.5, 0);
INSERT INTO ecommerce_product_type VALUES (2, 'Software', 17.5, 0);
INSERT INTO ecommerce_product_type VALUES (3, 'Energy', 5, 0);
INSERT INTO ecommerce_product_type VALUES (4, 'Software (only download)', 17.5, 0);
INSERT INTO ecommerce_product_type VALUES (5, 'Documents  (download)', 17.5, 0);
INSERT INTO ecommerce_product_type VALUES (6, 'books', 0, 0);
INSERT INTO ecommerce_product_type VALUES (7, 'Food', 17.5, 0);
INSERT INTO ecommerce_product_type VALUES (8, 'Food BIO', 5, 0);
INSERT INTO ecommerce_product_type VALUES (9, 'Generic 1', 17.5, 1);
INSERT INTO ecommerce_product_type VALUES (10, 'Generic 2', 5, 1);
INSERT INTO ecommerce_product_type VALUES (11, 'Generic 0', 0, 1);

SELECT pg_catalog.setval('ecommerce_product_type_id_seq', (SELECT max(id) FROM ecommerce_product_type), true);

/*ecommerce_product*/

/*ecommerce_product_taxonomy*/

/*ecommerce_product_variety*/

/*ecommerce_price*/

/*ecommerce_basket*/

/*ecommerce_basket_content*/

/*ecommerce_order*/

/*ecommerce_delivery_carrier*/
INSERT INTO ecommerce_delivery_carrier VALUES (1, 'Standard');
INSERT INTO ecommerce_delivery_carrier VALUES (2, 'Royal Mail 1st Class Post');
INSERT INTO ecommerce_delivery_carrier VALUES (3, 'DHL Courier');
INSERT INTO ecommerce_delivery_carrier VALUES (4, 'UPS');
INSERT INTO ecommerce_delivery_carrier VALUES (5, 'Courier');
INSERT INTO ecommerce_delivery_carrier VALUES (6, 'Download');

SELECT pg_catalog.setval('ecommerce_delivery_carrier_id_seq', (SELECT max(id) FROM ecommerce_delivery_carrier), true);

/*ecommerce_delivery_carrier_zone*/

/*ecommerce_delivery_carrier_zone_to_country*/

/*ecommerce_delivery_carrier_zone_price*/

/*ecommerce_delivery*/

/*ecommerce_order_log*/

/*ecommerce_invoice*/

/*ecommerce_transaction*/

/*ecommerce_product_to_product*/

/*ecommerce_product_image*/

/*ecommerce_product_variety_image*/

/*ecommerce_product_variety_taxonomy*/

/*ecommerce_product_review*/

/*ecommerce_promotion*/

/*ecommerce_promotion_code*/


COMMIT;
