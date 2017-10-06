ALTER TABLE common_node ADD COLUMN css_class varchar(255);
ALTER TABLE common_node ADD COLUMN layout_style varchar(255);
ALTER TABLE common_node ADD COLUMN component text;
ALTER TABLE common_node ADD COLUMN relations text;
ALTER TABLE common_node ADD COLUMN display_title smallint;
ALTER TABLE common_node ADD COLUMN display_secondary_navigation smallint;
ALTER TABLE common_node ADD COLUMN require_login smallint;
ALTER TABLE ecommerce_basket_content ADD COLUMN product_type_id smallint REFERENCES ecommerce_product_type ON UPDATE CASCADE ON DELETE RESTRICT;
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'product_highlights';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'component';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'contact_form';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'divider';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'teaser';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'RSS';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'quote';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'product_list';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'picture';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'news_list';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'menu';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'imagemap';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'content' AND layout_template = 'filter';
UPDATE common_node SET component = other_data, other_data = '' WHERE node_type = 'page' AND layout_template = 'news';
UPDATE common_node SET component = content, content = '' WHERE node_type = 'page' AND layout_template = 'symbolic';

UPDATE ecommerce_basket_content SET product_type_id = 1 WHERE product_variety_id IN (SELECT ecommerce_product_variety.id FROM ecommerce_basket_content LEFT OUTER JOIN ecommerce_product_variety ON (ecommerce_product_variety.id = ecommerce_basket_content.product_variety_id) LEFT OUTER JOIN ecommerce_product  ON (ecommerce_product.id = ecommerce_product_variety.product_id) WHERE ecommerce_product.product_type_id = 1);
UPDATE ecommerce_basket_content SET product_type_id = 2 WHERE product_variety_id IN (SELECT ecommerce_product_variety.id FROM ecommerce_basket_content LEFT OUTER JOIN ecommerce_product_variety ON (ecommerce_product_variety.id = ecommerce_basket_content.product_variety_id) LEFT OUTER JOIN ecommerce_product  ON (ecommerce_product.id = ecommerce_product_variety.product_id) WHERE ecommerce_product.product_type_id = 2);
UPDATE ecommerce_basket_content SET product_type_id = 3 WHERE product_variety_id IN (SELECT ecommerce_product_variety.id FROM ecommerce_basket_content LEFT OUTER JOIN ecommerce_product_variety ON (ecommerce_product_variety.id = ecommerce_basket_content.product_variety_id) LEFT OUTER JOIN ecommerce_product  ON (ecommerce_product.id = ecommerce_product_variety.product_id) WHERE ecommerce_product.product_type_id = 3);
UPDATE ecommerce_basket_content SET product_type_id = 4 WHERE product_variety_id IN (SELECT ecommerce_product_variety.id FROM ecommerce_basket_content LEFT OUTER JOIN ecommerce_product_variety ON (ecommerce_product_variety.id = ecommerce_basket_content.product_variety_id) LEFT OUTER JOIN ecommerce_product  ON (ecommerce_product.id = ecommerce_product_variety.product_id) WHERE ecommerce_product.product_type_id = 4);
UPDATE ecommerce_basket_content SET product_type_id = 5 WHERE product_variety_id IN (SELECT ecommerce_product_variety.id FROM ecommerce_basket_content LEFT OUTER JOIN ecommerce_product_variety ON (ecommerce_product_variety.id = ecommerce_basket_content.product_variety_id) LEFT OUTER JOIN ecommerce_product  ON (ecommerce_product.id = ecommerce_product_variety.product_id) WHERE ecommerce_product.product_type_id = 5);
UPDATE ecommerce_basket_content SET product_type_id = 6 WHERE product_variety_id IN (SELECT ecommerce_product_variety.id FROM ecommerce_basket_content LEFT OUTER JOIN ecommerce_product_variety ON (ecommerce_product_variety.id = ecommerce_basket_content.product_variety_id) LEFT OUTER JOIN ecommerce_product  ON (ecommerce_product.id = ecommerce_product_variety.product_id) WHERE ecommerce_product.product_type_id = 6);
UPDATE ecommerce_basket_content SET product_type_id = 7 WHERE product_variety_id IN (SELECT ecommerce_product_variety.id FROM ecommerce_basket_content LEFT OUTER JOIN ecommerce_product_variety ON (ecommerce_product_variety.id = ecommerce_basket_content.product_variety_id) LEFT OUTER JOIN ecommerce_product  ON (ecommerce_product.id = ecommerce_product_variety.product_id) WHERE ecommerce_product.product_type_id = 7);
UPDATE ecommerce_basket_content SET product_type_id = 8 WHERE product_variety_id IN (SELECT ecommerce_product_variety.id FROM ecommerce_basket_content LEFT OUTER JOIN ecommerce_product_variety ON (ecommerce_product_variety.id = ecommerce_basket_content.product_variety_id) LEFT OUTER JOIN ecommerce_product  ON (ecommerce_product.id = ecommerce_product_variety.product_id) WHERE ecommerce_product.product_type_id = 8);
UPDATE ecommerce_basket_content SET product_type_id = 9 WHERE product_variety_id IN (SELECT ecommerce_product_variety.id FROM ecommerce_basket_content LEFT OUTER JOIN ecommerce_product_variety ON (ecommerce_product_variety.id = ecommerce_basket_content.product_variety_id) LEFT OUTER JOIN ecommerce_product  ON (ecommerce_product.id = ecommerce_product_variety.product_id) WHERE ecommerce_product.product_type_id = 9);
UPDATE ecommerce_basket_content SET product_type_id = 10 WHERE product_variety_id IN (SELECT ecommerce_product_variety.id FROM ecommerce_basket_content LEFT OUTER JOIN ecommerce_product_variety ON (ecommerce_product_variety.id = ecommerce_basket_content.product_variety_id) LEFT OUTER JOIN ecommerce_product  ON (ecommerce_product.id = ecommerce_product_variety.product_id) WHERE ecommerce_product.product_type_id = 10);
UPDATE ecommerce_basket_content SET product_type_id = 11 WHERE product_variety_id IN (SELECT ecommerce_product_variety.id FROM ecommerce_basket_content LEFT OUTER JOIN ecommerce_product_variety ON (ecommerce_product_variety.id = ecommerce_basket_content.product_variety_id) LEFT OUTER JOIN ecommerce_product  ON (ecommerce_product.id = ecommerce_product_variety.product_id) WHERE ecommerce_product.product_type_id = 11);

/**
with_navigation -> default
0,2 -> 1
1 -> 2
3 -> 4
*/
UPDATE common_node SET parent_container = 4 WHERE parent_container = 3 AND parent IN (SELECT id FROM common_node WHERE layout_template = 'with_navigation');
UPDATE common_node SET parent_container = 20 WHERE parent_container = 1 AND parent IN (SELECT id FROM common_node WHERE layout_template = 'with_navigation');
UPDATE common_node SET parent_container = 1 WHERE (parent_container = 0 OR parent_container = 2) AND parent IN (SELECT id FROM common_node WHERE layout_template = 'with_navigation');
UPDATE common_node SET parent_container = 2 WHERE parent_container = 20 AND parent IN (SELECT id FROM common_node WHERE layout_template = 'with_navigation');
UPDATE common_node SET layout_template = 'default', layout_style = 'seventy-thirty' WHERE layout_template = 'with_navigation';

/*
product_browse, commerce, news
0,2 -> 1
1 -> 2
3 -> 4
*/
UPDATE common_node SET parent_container = 4 WHERE parent_container = 3 AND (parent IN (SELECT id FROM common_node WHERE layout_template = 'product_browse') OR parent IN (SELECT id FROM common_node WHERE layout_template = 'commerce') OR parent IN (SELECT id FROM common_node WHERE layout_template = 'news'));
UPDATE common_node SET parent_container = 20 WHERE parent_container = 1 AND (parent IN (SELECT id FROM common_node WHERE layout_template = 'product_browse') OR parent IN (SELECT id FROM common_node WHERE layout_template = 'commerce') OR parent IN (SELECT id FROM common_node WHERE layout_template = 'news'));
UPDATE common_node SET parent_container = 1 WHERE (parent_container = 0 OR parent_container = 2) AND (parent IN (SELECT id FROM common_node WHERE layout_template = 'product_browse') OR parent IN (SELECT id FROM common_node WHERE layout_template = 'commerce') OR parent IN (SELECT id FROM common_node WHERE layout_template = 'news'));
UPDATE common_node SET parent_container = 2 WHERE parent_container = 20 AND (parent IN (SELECT id FROM common_node WHERE layout_template = 'product_browse') OR parent IN (SELECT id FROM common_node WHERE layout_template = 'commerce') OR parent IN (SELECT id FROM common_node WHERE layout_template = 'news'));
UPDATE common_node SET layout_style = 'seventy-thirty' WHERE layout_template = 'product_browse' OR layout_template = 'commerce' OR layout_template = 'news';

/*
product 
3->5
1->2
*/
UPDATE common_node SET parent_container = 5 WHERE parent_container = 3 AND parent IN (SELECT id FROM common_node WHERE layout_template = 'product');
UPDATE common_node SET parent_container = 2 WHERE parent_container = 1 AND parent IN (SELECT id FROM common_node WHERE layout_template = 'product');
UPDATE common_node SET layout_style = 'seventy-thirty' WHERE layout_template = 'product';

