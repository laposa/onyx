BEGIN;
DELETE FROM ecommerce_delivery_carrier_zone_price WHERE id > 6;
UPDATE ecommerce_delivery_carrier_zone_price SET zone_id = 1, weight = 0, price = 1.90 WHERE id = 1;
UPDATE ecommerce_delivery_carrier_zone_price SET zone_id = 1, weight = 1, price = 1.40 WHERE id = 2;
UPDATE ecommerce_delivery_carrier_zone_price SET zone_id = 2, weight = 0, price = 2.90 WHERE id = 3;
UPDATE ecommerce_delivery_carrier_zone_price SET zone_id = 2, weight = 1, price = 3.60 WHERE id = 4;
UPDATE ecommerce_delivery_carrier_zone_price SET zone_id = 3, weight = 0, price = 3.90 WHERE id = 5;
UPDATE ecommerce_delivery_carrier_zone_price SET zone_id = 3, weight = 1, price = 7.50 WHERE id = 6;


UPDATE ecommerce_delivery_carrier_zone_to_country SET zone_id = 2 
WHERE country_id IN (5, 14, 21, 57, 70, 72, 73, 141, 81, 84, 85, 98, 103, 105, 182, 228, 124, 150, 160, 171, 74, 195, 203, 204, 122);
UPDATE ecommerce_delivery_carrier_zone_to_country SET zone_id = 3 
WHERE id != 222 AND country_id NOT IN (5, 14, 21, 57, 70, 72, 73, 141, 81, 84, 85, 98, 103, 105, 182, 228, 124, 150, 160, 171, 74, 195, 203, 204, 122);;

DELETE FROM ecommerce_delivery_carrier_zone WHERE id > 3;
UPDATE ecommerce_delivery_carrier_zone SET name = 'Western Europe' WHERE id = 2;
UPDATE ecommerce_delivery_carrier_zone SET name = 'Rest of the World' WHERE id = 3;
COMMIT;
