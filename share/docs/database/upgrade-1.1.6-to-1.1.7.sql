ALTER TABLE ecommerce_basket ADD COLUMN discount_net decimal(12,5) NOT NULL DEFAULT 0;
ALTER TABLE ecommerce_order RENAME promotional_code TO promotion_code; 

