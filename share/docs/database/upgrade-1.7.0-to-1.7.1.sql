BEGIN;

DELETE FROM ecommerce_offer WHERE id IN (
	SELECT DISTINCT a.id
	FROM ecommerce_offer AS a, ecommerce_offer AS b
	WHERE a.offer_group_id = b.offer_group_id
	AND a.product_variety_id = b.product_variety_id
	AND a.id < b.id
	ORDER by a.id
);

ALTER TABLE ONLY ecommerce_offer ADD CONSTRAINT offer_group_id_product_variety_id_key UNIQUE (offer_group_id, product_variety_id);

COMMIT;
