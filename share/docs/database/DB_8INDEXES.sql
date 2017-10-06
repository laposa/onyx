BEGIN;
CREATE INDEX common_node_parent_idx ON common_node (parent);
CREATE INDEX common_node_node_type_idx ON common_node (node_type);
CREATE INDEX common_node_publish_idx ON common_node (publish);
CREATE INDEX common_node_display_in_idx ON common_node (display_in);

CREATE INDEX common_image_node_id_idx ON common_image (node_id);

CREATE INDEX common_file_node_id_idx ON common_file (node_id);

CREATE INDEX common_print_article_node_id_idx ON common_print_article (node_id);

CREATE INDEX common_uri_mapping_node_id_idx ON common_uri_mapping (node_id);

CREATE INDEX common_node_taxonomy_node_id_idx ON common_node_taxonomy (node_id);
CREATE INDEX common_node_taxonomy_taxonomy_tree_id_idx ON common_node_taxonomy (taxonomy_tree_id);

CREATE INDEX common_taxonomy_tree_label_id_idx ON common_taxonomy_tree (label_id);
CREATE INDEX common_taxonomy_tree_parent_idx ON common_taxonomy_tree (parent);

CREATE INDEX common_session_session_id_idx ON common_session (session_id);
CREATE INDEX common_session_modified_idx ON common_session (modified);
COMMIT;

BEGIN;
CREATE INDEX client_company_customer_id_idx ON client_company (customer_id);

CREATE INDEX client_address_customer_id_idx ON client_address (customer_id);
CREATE INDEX client_address_country_id_idx ON client_address (country_id);
COMMIT;

BEGIN;
CREATE INDEX ecommerce_product_product_type_id_idx ON ecommerce_product (product_type_id);

CREATE INDEX ecommerce_product_publish_idx ON ecommerce_product (publish);

CREATE INDEX ecommerce_product_variety_product_id_idx ON ecommerce_product_variety (product_id);

CREATE INDEX ecommerce_price_product_variety_id_idx ON ecommerce_price (product_variety_id);
CREATE INDEX ecommerce_price_currency_code_idx ON ecommerce_price (currency_code);
CREATE INDEX ecommerce_price_type_idx ON ecommerce_price (type);

CREATE INDEX ecommerce_basket_customer_id_idx ON ecommerce_basket (customer_id);

CREATE INDEX ecommerce_basket_content_basket_id_idx ON ecommerce_basket_content (basket_id);
CREATE INDEX ecommerce_basket_content_product_variety_id_idx ON ecommerce_basket_content (product_variety_id);
CREATE INDEX ecommerce_basket_content_price_id_idx ON ecommerce_basket_content (price_id);

CREATE INDEX ecommerce_invoice_order_id_idx ON ecommerce_invoice (order_id);

CREATE INDEX ecommerce_order_basket_id_idx ON ecommerce_order (basket_id);
CREATE INDEX ecommerce_order_invoices_address_id_idx ON ecommerce_order (invoices_address_id);
CREATE INDEX ecommerce_order_delivery_address_id_idx ON ecommerce_order (delivery_address_id);

CREATE INDEX ecommerce_order_log_order_id_idx ON ecommerce_order_log (order_id);
CREATE INDEX ecommerce_order_log_status_idx ON ecommerce_order_log (status);

CREATE INDEX ecommerce_transaction_order_id_idx ON ecommerce_transaction (order_id);

CREATE INDEX ecommerce_product_to_node_from_id_idx ON ecommerce_product_to_node (from_id);
CREATE INDEX ecommerce_product_to_node_to_id_idx ON ecommerce_product_to_node (to_id);

CREATE INDEX ecommerce_product_to_product_product_id_idx ON ecommerce_product_to_product (product_id);
CREATE INDEX ecommerce_product_to_product_related_product_id_idx ON ecommerce_product_to_product (related_product_id);

CREATE INDEX ecommerce_product_image_node_id_idx ON ecommerce_product_image (node_id);



CREATE INDEX ecommerce_product_variety_image_node_id_idx ON ecommerce_product_variety_image (node_id);

CREATE INDEX ecommerce_product_taxonomy_node_id_idx ON ecommerce_product_taxonomy (node_id);
CREATE INDEX ecommerce_product_taxonomy_taxonomy_tree_id_idx ON ecommerce_product_taxonomy (taxonomy_tree_id);

CREATE INDEX ecommerce_product_variety_taxonomy_node_id_idx ON ecommerce_product_variety_taxonomy (node_id);
CREATE INDEX ecommerce_product_variety_taxonomy_taxonomy_tree_id_idx ON ecommerce_product_variety_taxonomy (taxonomy_tree_id);
COMMIT;

BEGIN;
CREATE INDEX common_comment_parent_idx ON common_comment (parent);
CREATE INDEX common_comment_node_id_idx ON common_comment (node_id);
CREATE INDEX common_comment_costomer_id_id_idx ON common_comment (customer_id);
COMMIT;
